<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventListenerInterface as Event;
use Zend\Framework\EventManager\PriorityQueue\ListenerTrait as ListenerService;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

trait ManagerTrait
{
    /**
     *
     */
    use ListenerService {
        ListenerService::add    as addToQueue;
        ListenerService::remove as removeFromQueue;
    }

    /**
     * @var array self
     */
    protected $shared = [];

    /**
     * Attach listener
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function add(ListenerInterface $listener)
    {
        if ($listener instanceof PriorityQueue\EventListenerInterface) {
            $this->shared[] = $listener;
            return $this;
        }

        return $this->addToQueue($listener);
    }

    /**
     * @param ListenerInterface $listener
     * @return self
     */
    public function remove(ListenerInterface $listener)
    {
        //if (in_array($listeners, $this->listeners)) {
        //fixme!
        //}

        return $this->removeFromQueue($listener);
    }

    /**
     * @param EventInterface $event
     * @param PriorityQueue $queue
     */
    public function matchShared(EventInterface $event, PriorityQueue $queue)
    {
        $name   = $event->name();
        $target = $event->target();

        $names = Event::WILDCARD == $name ? [$name] : [Event::WILDCARD, $name];

        foreach($this->shared as $shared) {
            foreach($names as $name) {
                if (!isset($shared->listeners[$name])) {
                    continue;
                }

                $this->match($target, $shared->listeners[$name], $queue);
            }
        }
    }

    /**
     * @param EventInterface $event
     * @return PriorityQueue
     */
    public function listeners(EventInterface $event)
    {
        $queue = new PriorityQueue;

        if ($this->shared) {
            $this->matchShared($event, $queue);
        }

        return $this->priorityQueue($event, $queue);
    }

    /**
     * @param EventInterface $event
     * @return bool
     */
    public function trigger(EventInterface $event)
    {
        return $this->__invoke($event);
    }
}
