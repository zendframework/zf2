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
    public function name();

    /**
     * Event name
     *
     * @param $name
     * @return self
     */
    public function setName($name);

    /**
     * Event target
     *
     * @return string|array|object|self::WILDCARD
     */
    public function target();

    /**
     * Event target
     *
     * @param $target
     * @return self
     */
    public function setTarget($target);
}
