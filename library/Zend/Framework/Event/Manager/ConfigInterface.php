<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Serializable;

interface ConfigInterface
    extends Serializable
{
    /**
     * Default priority
     *
     */
    const PRIORITY = 0;

    /**
     * @param string $name
     * @param string|callable $listener
     * @param $priority
     * @return self
     */
    public function add($name, $listener, $priority = self::PRIORITY);

    /**
     * @param string $name
     * @return array
     */
    public function get($name);

    /**
     * @param $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string|callable $listener
     * @return self
     */
    public function remove($listener);
}
