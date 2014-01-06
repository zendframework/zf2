<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\EventManager;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Stdlib\SplPriorityQueue as PriorityQueue;

interface ListenerInterface
    extends Listener
{
    /**
     * Push listener to top of queue
     *
     * @param Listener $listener
     * @return self
     */
    public function push(Listener $listener);

    /**
     * Add
     *
     * @param Listener $listener
     * @return self
     */
    public function add(Listener $listener);

    /**
     * Remove
     *
     * @param Listener $listener
     * @return self
     */
    public function remove(Listener $listener);

    /**
     * @param $listener
     * @return mixed
     */
    public function listener($listener);

    /**
     * Listeners
     *
     * @param Event $event
     * @return PriorityQueue
     */
    public function listeners(Event $event);

    /**
     * Queue listeners
     *
     * @param Event $event
     * @param PriorityQueue $queue
     * @return PriorityQueue
     */
    public function queue(Event $event, PriorityQueue $queue);

    /**
     * Trigger
     *
     * @param Event $event
     * @return bool stopped
     */
    public function __invoke(Event $event);
}
