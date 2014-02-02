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
     * @param array $config
     * @return self
     */
    public function config(array $config);

    /**
     * @param string $name
     * @param string $class
     */
    public function configure($name, $class);

    /**
     * @param string|callable $factory
     * @return callable
     */
    public function factory($factory);

    /**
     * @param string $name
     * @param array $options
     * @param bool $shared
     * @return object
     */
    public function get($name, array $options = [], $shared = true);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param $name
     * @return mixed
     */
    public function service($name);

    /**
     * @param RequestInterface $event
     * @return mixed
     */
    public function request(RequestInterface $event);
}
