<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\PriorityQueue;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService;

    /**
     * @var array Listener
     */
    protected $listeners = [];

    /**
     * Add listener
     *
     * @param Listener $listener
     * @return self
     */
    public function addListener(Listener $listener)
    {
        $event    = $listener->getEventNames();
        $priority = $listener->getEventPriority();

        foreach($event as $name) {
            $this->listeners[$name][$priority][] = $listener;
        }

        return $this;
    }

    /**
     * Remove listener
     *
     * @param Listener $listener
     * @return self
     */
    public function removeListener(Listener $listener)
    {
        return $this;
    }

    /**
     * @param Event $event
     * @param PriorityQueue $queue
     * @return PriorityQueue
     */
    public function getEventListeners(Event $event, PriorityQueue $queue = null)
    {
        if (null === $queue) {
            $queue = new PriorityQueue;
        }

        $name   = $event->getEventName();
        $target = $event->getEventTarget();

        $names = Event::WILDCARD == $name ? [$name] : [Event::WILDCARD, $name];

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                continue;
            }

            foreach($this->listeners[$name] as $priority => $listeners) {
                foreach($listeners as $listener) {
                    foreach($listener->getEventTargets() as $t) {
                        if (Listener::WILDCARD === $t || $target === $t || $target instanceof $t || \is_subclass_of($target, $t)) {
                            $queue->insert($listener, $priority);
                        }
                    }
                }
            }
        }

        return $queue;
    }
}
