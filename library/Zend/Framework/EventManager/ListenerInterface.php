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

interface ListenerInterface
    extends EventListenerInterface
{
    /**
     *
     */
    const DEFAULT_PRIORITY = 0;

    /**
     * Priority of listener
     *
     * @return int
     */
    public function getEventPriority();

    /**
     * Priority of listener
     *
     * @param $priority
     * @return Listener
     */
    public function setEventPriority($priority);

    /**
     * Array of event names to listen for
     *
     * @return array
     */
    public function getEventNames();

    /**
     * Array of target identifiers to listener for
     *
     * @return array
     */
    public function getEventTargets();

    /**
     * Invokes listener with the event
     *
     * @param Event $event
     * @return mixed
     */
    public function __invoke(Event $event);
}
