<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

interface ListenerInterface
{
    /**
     * Priority default
     *
     */
    const PRIORITY = 0;

    /**
     * Priority default
     *
     */
    const STOPPED = '1869f';

    /**
     * Wildcard
     *
     */
    const WILDCARD = '*';

    /**
     * @param $target
     * @return bool
     */
    public function matchTarget($target);

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
}
