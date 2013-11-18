<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayAccess;

/**
 * Representation of an listener
 */
interface ListenerInterface
{
    /**
     *
     */
    const WILDCARD_NAME = '*';

    /**
     *
     */
    const DEFAULT_PRIORITY = 1;

    /**
     * Name(s) of event to listener for
     *
     * @return mixed
     */
    public function getEventName();

    /**
     * Name(s) of event to listener for
     *
     * @return mixed
     */
    public function setEventName($name);

    /**
     * Array of name(s) of event to listener for
     *
     * @return array
     */
    public function getEventNames();

    /**
     * Priority of listener
     *
     * @return mixed
     */
    public function getPriority();

    /**
     * Priority of listener
     *
     * @return mixed
     */
    public function setPriority($priority);

    /**
     * Target (identifiers) to listener for
     *
     * @return mixed
     */
    public function getTarget();

    /**
     * Target (identifiers) to listener for
     *
     * @return mixed
     */
    public function setTarget($target);

    /**
     * Array of target (identifiers) to listener for
     *
     * @return array
     */
    public function getTargets();

    /**
     * Invokes listener with the event
     *
     * @param $event
     * @return mixed
     */
    public function __invoke($event);
}
