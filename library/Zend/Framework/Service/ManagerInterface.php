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
     * Shared service
     *
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service);

    /**
     * @return ConfigInterface
     */
    public function config();

    /**
     * @param string $name
     * @param array $options
     * @param bool $shared
     * @return object
     */
    public function get($name, array $options = [], $shared = true);

    /**
     * Shared service
     *
     * @param string $name
     * @return bool
     */
    public function has($name);
}
