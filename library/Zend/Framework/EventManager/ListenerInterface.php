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
{
    /**
     *
     */
    const WILDCARD = '*';

    /**
     *
     */
    const DEFAULT_PRIORITY = 0;

    /**
     * Name(s) of event to listen for
     *
     * @return string|array
     */
    public function getEventName();

    /**
     * Name(s) of event to listen for
     *
     * @param $name
     * @return Listener
     */
    public function setEventName($name);

    /**
     * Array of event names to listen for
     *
     * @return array
     */
    public function getEventNames();

    /**
     * Target identifiers
     *
     * @return string|array
     */
    public function getEventTarget();

    /**
     * Target identifiers to listener for
     *
     * @param $target
     * @return Listener
     */
    public function setEventTarget($target);

    /**
     * Array of target identifiers to listener for
     *
     * @return array
     */
    public function getEventTargets();

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
     * Invokes listener with the event
     *
     * @param Event $event
     * @return mixed
     */
    public function __invoke(Event $event);
}
