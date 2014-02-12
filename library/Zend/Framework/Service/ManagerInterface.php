<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

interface ManagerInterface
{
    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service);

    /**
     * @param string $name
     * @param mixed $options
     * @param bool $shared
     * @return false|object
     */
    public function get($name, $options = null, $shared = true);

    /**
     * @return ConfigInterface
     */
    public function services();
}
