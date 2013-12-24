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
    public function attach(Listener $listener)
    {
        if ($listener instanceof self) {
            $this->shared[] = $listener;
            return $this;
        }

        return parent::addListener($listener);
    }

    /**
     * @param Listener $listener
     * @return self
     */
    public function detach(Listener $listener)
    {
        //if (in_array($listeners, $this->listeners)) {
        //fixme!
        //}

        return parent::removeListener($listener);
    }

    /**
     * @param EventInterface $event
     * @param PriorityQueue $queue
     * @return PriorityQueue
     */
    public function getEventListeners(Event $event, PriorityQueue $queue = null)
    {
        $queue = new PriorityQueue;

        $name = $event->getEventName();

        foreach($this->shared as $shared) {
            foreach($shared->listeners[$name] as $priority => $listeners) {
                foreach($listeners as $listener) {
                    $queue->insert($listener, $priority);
                }
            }
        }

        return parent::getEventListeners($event, $queue);
    }

    /**
     * @param Event $event
     * @return bool event propagation was stopped
     */
    public function trigger(Event $event)
    {
        return $this->__invoke($event);
    }
}
