<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Traversable;
use SplPriorityQueue as PriorityQueue;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerAggregateInterface;

use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\EventManager\ManagerInterface as EventManagerInterface;

/**
 * Event manager: notification system
 *
 * Use the EventManager when you want to create a per-instance notification
 * system for your objects.
 */
class Manager
    extends EventListener
    implements EventManagerInterface
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

        if ($listener instanceof ListenerAggregateInterface) {
            $listener->attach($this);
            return;
        } elseif (is_array($listener) || $listener instanceof Traversable) {
            foreach($listener as $l) {
                $l->attach($this);
            }
            return;
        }

        $event    = $listener->getEventNames();
        $priority = $listener->getPriority();

        foreach($event as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = new PriorityQueue();
            }

            $this->listeners[$name]->insert($listener, $priority);
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

    public function getListeners($name)
    {
        if (isset($this->listeners[$name])) {
            return clone $this->listeners[$name];
        }

        return new PriorityQueue();
    }

    /**
     * @param $event Event
     * @return PriorityQueue
     */
    public function getEventListeners(Event $event)
    {
        $listeners = new PriorityQueue();

        $name    = $event->getName();
        $targets = $event->getTargets();

        foreach($this->shared as $shared) {
            foreach($shared->getEventListeners($event) as $listener) {
                $listeners->insert($listener, $listener->getPriority());
            }
        }

        $names = Event::WILDCARD == $name ? [$name] : [Event::WILDCARD, $name];

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                continue;
            }

            foreach(clone $this->listeners[$name] as $listener) {
                foreach($listener->getTargets() as $lt) {
                    if (Listener::WILDCARD === $lt) {
                        $listeners->insert($listener, $listener->getPriority());
                        continue;
                    }
                    foreach($targets as $t) {
                        if ($t === $lt || \is_subclass_of($t, $lt)) {
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
     * @param Event $event
     */
    public function trigger(Event $event)
    {
        $event->stopPropagation(false);

        $listeners = $event->getListeners() ?: $this->getEventListeners($event);

        foreach($listeners as $listener) {
            //var_dump(get_class($event) . ' :: ' .$event->getName() . ' :: ' . get_class($listener));
            $event($listener);

            if ($event->propagationIsStopped()) {
                break;
            }
        }
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $this->trigger($event);
    }
}
