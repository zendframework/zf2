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
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;
use Zend\Framework\EventManager\PriorityQueue\Listener as PriorityQueueListener;

class Manager
    extends PriorityQueueListener
    implements ManagerInterface
{

    /**
     * @var array self
     */
    protected $shared = [];

    /**
     * Attach listener
     *
     * @param Listener $listener
     * @return self
     */
    public function add(Listener $listener)
    {
        if ($listener instanceof self) {
            $this->shared[] = $listener;
            return $this;
        }

        return parent::add($listener);
    }

    /**
     * @param Listener $listener
     * @return self
     */
    public function remove(Listener $listener)
    {
        //if (in_array($listeners, $this->listeners)) {
        //fixme!
        //}

        return parent::remove($listener);
    }

    /**
     * @param EventInterface $event
     * @return PriorityQueue
     */
    public function listeners(Event $event)
    {
        $queue = new PriorityQueue;

        $name = $event->name();

        foreach($this->shared as $shared) {
            foreach($shared->listeners[$name] as $priority => $listeners) {
                foreach($listeners as $listener) {
                    $queue->insert($listener, $priority);
                }
            }
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
