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
use Zend\Framework\EventManager\ManagerInterface as EventManagerInterface;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

class Manager
    extends PriorityQueueListener
    implements EventManagerInterface
{

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
        if ($listener instanceof self) {
            $this->shared[] = $listener;
            return $this;
        }

        return parent::addListener($listener);
    }

    /**
     * Detach listener
     *
     * @param Listener $listener
     */
    public function detach(Listener $listener)
    {
        //if (in_array($listeners, $this->listeners)) {
            //fixme!
        //}

        return parent::removeListener($listener);
    }

    /**
     * @param Event $event
     * @return PriorityQueue
     */
    public function getEventListeners(Event $event)
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
     * @return void
     */
    public function trigger(Event $event)
    {
        $this->__invoke($event);
    }
}
