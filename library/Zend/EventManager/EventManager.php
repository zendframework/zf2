<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayAccess;
use ArrayObject;
use Traversable;
use Zend\Stdlib\CallbackHandler;
use Zend\Stdlib\PriorityQueue;

/**
 * Event manager: notification system
 *
 * Use the EventManager when you want to create a per-instance notification
 * system for your objects.
 */
class EventManager extends Listener implements EventManagerInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $shared_listeners = [];

    /**
     * @param  mixed $target
     */
    public function __construct($target = null)
    {
        $this->setTarget($target);
    }

    /**
     * Attach listener(s)
     *
     * @param array|Traversable $listener
     */
    public function attach($listener)
    {
        if ($listener instanceof EventManagerInterface) {
            $this->shared_listeners[] = $listener;
            return;
        }

        if (is_array($listener) || $listener instanceof Traversable) {
            foreach($listener as $l) {
                $this->attach($l);
            }
            return;
        }

        $event    = $listener->getEventName();
        $priority = $listener->getPriority();

        if (!is_array($event)) {
            $event = [$event];
        }

        foreach($event as $name) {
            $this->listeners[$name][$priority][] = $listener;
        }
    }

    /**
     * @param $listener
     */
    public function detach($listener)
    {
        $event    = $listener->getEventName();
        $priority = $listener->getPriority();

        if (!is_array($event)) {
            $event = [$event];
        }

        foreach($event as $name) {
            if (isset($this->listeners[$name][$priority])) {
                $listeners = [];

                foreach($this->listeners[$name][$priority] as $l) {
                    if ($l !== $listener) {
                        $listeners[] = $l;
                    }
                }

                if (!$listeners) {
                    unset($this->listeners[$name][$priority]);
                    continue;
                }

                $this->listeners[$name][$priority] = $listeners;
            }
        }
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        $listeners = $this->listeners;

        foreach($this->shared_listeners as $shared_listener) {
            foreach($shared_listener->getListeners() as $event => $priority_listeners) {
                foreach($priority_listeners as $priority => $l) {
                    foreach($l as $listener) {
                        $listeners[$event][$priority][] = $listener;
                    }
                }
            }
        }

        return $listeners;
    }

    /**
     * @param string|array|Event $event
     * @return array
     */
    public function getEventListeners($event)
    {
        $prioritized = [];

        $event_listeners = $this->getListeners();

        $target = $event->getTarget();

        if ($target && !is_array($target)) {
            $target = [$target];
        }

        if (isset($event_listeners[Event::WILDCARD_NAME])) {
            foreach($target as $t) {
                foreach($event_listeners[Event::WILDCARD_NAME] as $priority => $p) {
                    foreach($p as $listener) {

                        $lt = $listener->getTarget();

                        if (!is_array($lt)) {
                           $lt = [$lt];
                        }

                        foreach($lt as $pt) {
                            if ('*' === $pt || $t === $pt || $pt instanceof $t) {
                                $prioritized[$priority][] = $listener;
                            }
                        }
                    }
                }
            }

            unset($event_listeners[Event::WILDCARD_NAME]);
        }

        $name = is_string($event) ? $event : (is_array($event) ? $event : $event->getName());

        if (!is_array($name)) {
            $name = [$name];
        }

        foreach($name as $n) {
            if (!isset($event_listeners[$n])) {
                continue;
            }

            foreach($event_listeners[$n] as $priority => $p) {
                foreach($target as $t) {
                    foreach($p as $listener) {

                        $lt = $listener->getTarget();

                        if (!is_array($lt)) {
                            $lt = [$lt];
                        }

                        foreach($lt as $pt) {
                            if ('*' === $pt || $t === $pt || $pt instanceof $t) {
                                $prioritized[$priority][] = $listener;
                            }
                        }
                    }
                }
            }
        }

        krsort($prioritized, SORT_NUMERIC);

        $listeners = [];

        foreach($prioritized as $priority => $prioritized_listeners) {
            foreach($prioritized_listeners as $priority_listener) {
                $listeners[] = $priority_listener;
            }
        }

        return $listeners;
    }

    /**
     * Event object contains its name, target(s) and listeners
     *
     * If this EventManager has targets, these will be set as the target(s) of the event
     *
     * @param Event $event
     */
    public function trigger($event)
    {
        if ($this->target) {
            $event->setTarget($this->getTarget());
        }

        // Initial value of stop propagation flag should be false
        $event->stopPropagation(false);

        $listeners = $event->getListeners() ?: $this->getEventListeners($event);

        foreach($listeners as $listener) {
            if ($event($listener)) {
                break;
            }
        }

        //return $event->getEventResponses();
    }

    /**
     * @param $event
     * @return mixed
     */
    public function __invoke($event)
    {
        return $this->trigger($event);
    }
}
