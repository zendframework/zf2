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
use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService;

    /**
     * @var array ListenerInterface
     */
    protected $listeners = [];

    /**
     * Add listener
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function push(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
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
     * @param string $target
     * @param array $listeners
     * @param PriorityQueue $queue
     */
    public function match($target, array $listeners, PriorityQueue $queue)
    {
        foreach($listeners as $priority => $listeners) {
            foreach($listeners as $listener) {
                foreach($listener->targets() as $t) {
                    if (
                        $t === ListenerInterface::WILDCARD
                        || $t === $target
                        || $target instanceof $t
                        || \is_subclass_of($target, $t)
                    ) {
                        $queue->insert($listener, $priority);
                    }
                }
            }
        }
    }

    /**
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
            if (!isset($this->listeners[$name])) {
                continue;
            }

            $this->match($target, $this->listeners[$name], $queue);
        }

        return $queue;
    }

    /**
     * @param EventInterface $event
     * @return PriorityQueue
     */
    public function listeners(EventInterface $event)
    {
        return $this->queue($event, new PriorityQueue);
    }

    /**
     * @param EventInterface $event
     * @return bool stopped
     */
    public function __invoke(EventInterface $event)
    {
        foreach($this->listeners($event) as $listener) {
            if ($event->__invoke($listener)) {
                return false; //event stopped
            }
        }

        return true;
    }
}
