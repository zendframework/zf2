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
use Zend\Framework\EventManager\ListenerInterface as EventListener;

use Traversable;

/**
 * Interface for messengers
 */
interface EventManagerInterface
{
    /**
     * @param EventListener $listener
     */
    public function attach($listener);

    /**
     * @param EventListener $listener
     */
    public function detach($listener);

    /**
     * @param Event $event
     * @return array|Traversable
     */
    public function getEventListeners(Event $event);

    /**
     * @param Event $event
     */
    public function __invoke(Event $event);
}
