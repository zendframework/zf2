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
     * @param Event $event
     * @return Generator
     */
    protected function match(Event $event)
    {
        foreach($this->queue($event->name()) as $listener) {
            if (!$listener instanceof ListenerInterface || $listener->target($event)) {
                yield $listener;
            }
        }
    }

    /**
     * @param string $event
     * @return Generator
     */
    protected function queue($event)
    {
        foreach($this->listeners()->get($event) as $listeners) {
            foreach($listeners as $listener) {
                yield $this->listener($listener);
            }
        }
    }

    /**
     * @param Event $event
     * @param null $options
     * @return mixed|null
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
