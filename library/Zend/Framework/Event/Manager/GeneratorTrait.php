<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Generator;
use Traversable;
use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Event\ListenerTrait as EventListener;

trait GeneratorTrait
{
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @param string|Listener $listener
     * @return Listener
     */
    abstract protected function listener($listener);

    /**
     * @return Config
     */
    abstract public function listeners();

    /**
     * @param array|Traversable $listeners
     * @return Generator
     */
    protected function generator($listeners)
    {
        foreach($listeners as $listener) {
            yield $this->listener($listener);
        }
    }

    /**
     * @param Event $event
     * @return Generator
     */
    protected function match(Event $event)
    {
        foreach($this->queue($event->name()) as $listeners) {
            foreach($this->generator($listeners) as $listener) {
                if ($listener->target($event)) {
                    yield $listener;
                }
            }
        }
    }

    /**
     * @param string $event
     * @return array|Traversable
     */
    protected function queue($event)
    {
        return $this->listeners()->get($event);
    }

    /**
     * @param Event $event
     * @param null $options
     * @param bool $shared
     * @return null
     */
    public function __invoke(Event $event, $options = null, $shared = false)
    {
        $name = $event->name();

        if ($shared && $name && isset($this->events[$name])) {
            return $this->events[$name];
        }

        $result = null;

        foreach($this->match($event) as $listener) {

            $result = $event->__invoke($listener, $options);

            if ($event->stopped()) {
                break;
            }
        }

        if ($shared && $name) {
            $this->events[$name] = $result;
        }

        return $result;
    }
}
