<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Config\ConfigTrait as Config;

trait ConfigTrait
{
    /**
     *
     */
    use Config;

    /**
     * @var array
     */
    protected $assigned = [];

    /**
     * @var array
     */
    protected $services;

    /**
     * @param string $name
     * @param mixed $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this
     */
    public function assign($name, callable $callable)
    {
        $this->assigned[$name] = $callable;
        return $this;
    }

    /**
     * @param string $name
     * @return callable|null
     */
    public function assigned($name)
    {
        return isset($this->assigned[$name]) ? $this->assigned[$name] : null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function configuration($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param string $name
     * @return object|null
     */
    public function service($name)
    {
        return isset($this->services[$name]) ? $this->services[$name] : null;
    }
}
