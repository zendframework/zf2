<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\PriorityQueue;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\ListenerInterface;
use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * Listeners
     *
     * @var array ListenerInterface
     */
    public $listeners = [];

    /**
     * @param $name
     * @param $priority
     * @param $listener
     */
    public function configure($name, $priority, $listener)
    {
        $this->listeners[$name][$priority][] = $listener;
    }

    /**
     * Push listener to top of queue
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function push(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = [];
            }
            if (!isset($this->listeners[$name][$priority])) {
                $this->listeners[$name][$priority][] = $listener;
                continue;
            }

            array_unshift($this->listeners[$name][$priority], $listener);
        }

        return $this;
    }

    /**
     * Add listener
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function add(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = [];
            }
            if (!isset($this->listeners[$name][$priority])) {
                $this->listeners[$name][$priority] = [];
            }
            $this->listeners[$name][$priority][] = $listener;
        }

        return $this;
    }

    /**
     * Remove listener
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function remove(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
            if (!isset($this->listeners[$name][$priority])) {
                continue;
            }

            $this->listeners[$name][$priority] = array_diff($this->listeners[$name][$priority], [$listener]);
        }

        return $this;
    }

    /**
     * Queue listeners
     *
     * @param EventInterface $event
     * @param PriorityQueue $queue
     * @return PriorityQueue
     */
    public function queue(EventInterface $event, PriorityQueue $queue)
    {
        $name   = $event->name();
        $target = $event->target();

        $names = EventInterface::WILDCARD == $name ? [$name] : [EventInterface::WILDCARD, $name];

        foreach($names as $name) {
            if (!isset($this->listeners[$name]) || !$this->listeners[$name]) {
                continue;
            }

            foreach($this->listeners[$name] as $priority => $priorityListeners) {
                foreach($priorityListeners as $index => $listener) {
                    $this->listeners[$name][$priority][$index] = $listener = $this->listener($listener);
                    foreach($listener->targets() as $t) {
                        if (
                            $t === ListenerInterface::WILDCARD
                            || $t === $target
                            || $target instanceof $t
                            || \is_subclass_of($target, $t)
                        ) {
                            $queue->insert($listener, $priority);
                            continue 2;
                        }
                    }
                }
            }
        }

        return $queue;
    }

    /**
     * @param $listener
     * @return mixed
     */
    public function listener($listener)
    {
        return $listener;
    }

    /**
     * Listeners
     *
     * @param EventInterface $event
     * @return PriorityQueue
     */
    public function listeners(EventInterface $event)
    {
        return $this->queue($event, new PriorityQueue);
    }

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return bool stopped
     */
    public function __invoke(EventInterface $event)
    {
        foreach($this->listeners($event) as $listener) {
            //var_dump($event->name().' :: '.get_class($event).' :: '.get_class($listener));
            if ($event->__invoke($listener)) {
                return false; //event stopped
            }
        }

        return true;
    }
}
