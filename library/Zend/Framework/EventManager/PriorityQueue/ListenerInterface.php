<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\PriorityQueue;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

interface ListenerInterface
    extends Listener
{
    /**
     * @param Listener $listener
     * @return self
     */
    public function addListener(Listener $listener);

    /**
     * @param Listener $listener
     * @return self
     */
    public function removeListener(Listener $listener);

    /**
     * @param Event $event
     * @return PriorityQueue
     */
    public function getEventListeners(Event $event, PriorityQueue $priorityQueue = null);
}
