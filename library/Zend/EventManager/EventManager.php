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
use SplPriorityQueue;

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
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = new SplPriorityQueue();
            }
            $this->listeners[$name]->insert($listener, $priority);
        }
    }

    /**
     * @param $listener
     */
    public function detach($listener)
    {
        //..
    }

    /**
     * @param string|array|Event $event
     * @return array
     */
    public function getEventListeners($event)
    {
        $listeners = new SplPriorityQueue();

        $name    = $event->getName();
        $targets = $event->getTargets();

        foreach($this->shared_listeners as $shared) {
            foreach($shared->getEventListeners($event) as $listener) {
                foreach($listener->getTargets() as $lt) {
                    if ('*' === $lt) {
                        $listeners->insert($listener, $listener->getPriority());
                        continue;
                    }
                    foreach($targets as $t) {
                        if ($t === $lt || $lt instanceof $t) {
                            $listeners->insert($listener, $listener->getPriority());
                            continue 2;
                        }
                    }
                }
            }
        }

        $names = '*' == $name ? [$name] : ['*', $name];

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                continue;
            }

            foreach($this->listeners[$name] as $listener) {
                foreach($listener->getTargets() as $lt) {
                    if ('*' === $lt) {
                        $listeners->insert($listener, $listener->getPriority());
                        continue;
                    }
                    foreach($targets as $t) {
                        if ($t === $lt || $lt instanceof $t) {
                            $listeners->insert($listener, $listener->getPriority());
                            continue 2;
                        }
                    }
                }
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
