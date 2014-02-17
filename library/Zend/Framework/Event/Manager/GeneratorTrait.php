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
use Zend\Framework\Event\ListenerInterface;

trait GeneratorTrait
{
    /**
     * @param $listener
     * @return callable|ListenerInterface
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
                if (!$listener instanceof ListenerInterface || $listener->target($event)) {
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
     * @return null
     */
    public function __invoke(Event $event, $options = null)
    {
        $result = null;

        foreach($this->match($event) as $listener) {

            $result = $event->call($listener, $options);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
