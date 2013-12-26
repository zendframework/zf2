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
     * Wildcard
     *
     */
    const WILDCARD = '*';

    /**
     * Name
     *
     * @return string|array
     */
    public function name();

    /**
     * Name set
     *
     * @param $name
     * @return self
     */
    public function setName($name);

    /**
     * Target
     *
     * @return string|array|object
     */
    public function target();

    /**
     * Target set
     *
     * @param $target
     * @return self
     */
    public function setTarget($target);
}
