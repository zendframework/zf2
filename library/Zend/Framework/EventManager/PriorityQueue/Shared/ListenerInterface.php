<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\PriorityQueue\Shared;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

interface ListenerInterface
    extends Listener
{

    /**
     * Add listener
     *
     * @param Listener $listener
     * @return self
     */
    public function add(Listener $listener);

    /**
     * Remove shared listener
     *
     * @param Listener $listener
     * @return $this
     */
    public function unshare(Listener $listener);

    /**
     * Remove listener
     *
     * @param Listener $listener
     * @return self
     */
    public function remove(Listener $listener);

    /**
     * Shared listeners
     *
     * @param Event $event
     * @param PriorityQueue $queue
     */
    public function shared(Event $event, PriorityQueue $queue);

    /**
     * Listeners
     *
     * @param Event $event
     * @return PriorityQueue
     */
    public function listeners(Event $event);
}
