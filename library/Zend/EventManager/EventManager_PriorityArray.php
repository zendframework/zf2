<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use Traversable;
use SplPriorityQueue as PriorityQueue;

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
     * @var array EventManager
     */
    protected $shared = [];

    /**
     * @param  mixed $target
     */
    public function __construct($target = null)
    {
        if (null !== $target) {
            $this->setTarget($target);
        }
    }

    /**
     * Attach listener(s)
     *
     * @param array|Traversable $listener
     * @return EventManager
     */
    public function attach($listener)
    {
        if ($listener instanceof EventManagerInterface) {
            $this->shared[] = $listener;
            return;
        }

        if (is_array($listener) || $listener instanceof Traversable) {
            foreach($listener as $l) {
                $this->attach($l);
            }
            return;
        }

        $event    = $listener->getEventNames();
        $priority = $listener->getPriority();

        foreach($event as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name][$priority] = [];
            }
            $this->listeners[$name][$priority][] = $listener;
        }

        return $this;
    }

    /**
     * @param $listener
     */
    public function detach($listener)
    {
        //..
    }

    /**
     * @param $event Event
     * @return PriorityQueue
     */
    public function getEventListeners($event)
    {
        $listeners = [];

        $name    = $event->getName();
        $targets = $event->getTargets();

        foreach($this->shared as $shared) {
            foreach($shared->getEventListeners($event) as $p) {
                foreach($p as $listener) {
                    $listeners[$listener->getPriority()][] = $listener;
                }
            }
        }

        $names = Event::WILDCARD == $name ? [$name] : [Event::WILDCARD, $name];

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                continue;
            }


            foreach($this->listeners[$name] as $priority => $p) {
                foreach($p as $listener) {
                    foreach($listener->getTargets() as $lt) {
                        if (Listener::WILDCARD === $lt) {
                            $listeners[$listener->getPriority()][] = $listener;
                            continue;
                        }
                        foreach($targets as $t) {
                            if ($t === $lt || $lt instanceof $t) {
                                $listeners[$listener->getPriority()][] = $listener;
                                continue 2;
                            }
                        }
                    }
                }
            }
        }

        krsort($listeners);

        return $listeners;
    }

    /**
     * Event object contains its name, target(s) and listeners
     *
     * The EventManager's target(s) will be set as the target(s) of the event
     *
     * Returns true if event propagation was stopped, otherwise it returns false
     *
     * @param Event $event
     * @return bool
     */
    public function trigger($event)
    {
        $event->setTarget($this->target)
              ->stopPropagation(false); // Initial value of stop propagation flag should be false

        $listeners = $event->getListeners() ?: $this->getEventListeners($event);

        foreach($listeners as $p) {
            foreach($p as $listener) {
                if ($event($listener)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $event
     * @return bool
     */
    public function __invoke($event)
    {
        return $this->trigger($event);
    }
}
