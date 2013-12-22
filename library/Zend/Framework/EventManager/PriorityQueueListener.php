<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\Framework\EventManager\ListenerTrait;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

class PriorityQueueListener
    implements PriorityQueueListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @var array Listener
     */
    protected $listeners = [];

    /**
     * @var array PriorityQueueListener
     */
    protected $shared = [];

    /**
     * Attach listener
     *
     * @param Listener $listener
     * @return $this
     */
    public function attach(Listener $listener)
    {
        if ($listener instanceof $this) {
            $this->shared[] = $listener;
            return;
        }

        $event    = $listener->getEventNames();
        $priority = $listener->getEventPriority();

        foreach($event as $name) {
            $this->listeners[$name][$priority][] = $listener;
        }

        return $this;
    }

    /**
     * Detach listener
     *
     * @param Listener $listener
     */
    public function detach(Listener $listener)
    {
        //...
    }

    /**
     * @param $event Event
     * @return PriorityQueue
     */
    public function getEventListeners(Event $event)
    {
        $listeners = new PriorityQueue;

        $name   = $event->getEventName();
        $target = $event->getEventTarget();

        foreach($this->shared as $shared) {
            foreach($shared->listeners[$name] as $priority => $shared) {
                foreach($shared as $listener) {
                    $listeners->insert($listener, $priority);
                }
            }
        }

        $names = Event::WILDCARD == $name ? [$name] : [Event::WILDCARD, $name];

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                continue;
            }

            foreach($this->listeners[$name] as $priority => $prioritized) {
                foreach($prioritized as $listener) {
                    foreach($listener->getEventTargets() as $t) {
                        if (Listener::WILDCARD === $t || $target === $t || \is_subclass_of($target, $t)) {
                            $listeners->insert($listener, $priority);
                        }
                    }
                }
            }
        }

        return $listeners;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        foreach($this->getEventListeners($event) as $listener) {
            if ($event($listener)) {
                break;
            }
        }
    }
}
