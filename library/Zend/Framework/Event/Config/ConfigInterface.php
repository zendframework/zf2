<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Config;

use Zend\Framework\Config\ConfigInterface as Config;

interface ConfigInterface
    extends Config
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
    public function queue($name);
}
