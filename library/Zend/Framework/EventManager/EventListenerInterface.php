<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

interface EventListenerInterface
{
    /**
     *
     */
    const WILDCARD = '*';

    /**
     * Event name
     *
     * @return string|array
     */
    public function getEventName();

    /**
     * Event name
     *
     * @param $name
     * @return self
     */
    public function setEventName($name);

    /**
     * Event target
     *
     * @return string|array|object
     */
    public function getEventTarget();

    /**
     * Event target
     *
     * @param $target
     * @return self
     */
    public function setEventTarget($target);
}
