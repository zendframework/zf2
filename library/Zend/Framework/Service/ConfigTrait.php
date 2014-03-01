<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Application\Config\ConfigInterface as ApplicationConfigInterface;
use Zend\Framework\Config\ConfigInterface;
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
    protected $pending = [];

    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * @param $name
     * @param $callable
     * @return $this
     */
    public function assign($name, callable $callable)
    {
        $this->assigned[$name] = $callable;
        return $this;
    }

    /**
     * @param $name
     * @return callable|null
     */
    public function assigned($name)
    {
        return isset($this->assigned[$name]) ? $this->assigned[$name] : null;
    }

    /**
     * @return ApplicationConfigInterface
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param $name
     * @return self
     */
    public function initialized($name)
    {
        $this->pending[$name] = false;
        return $this;
    }

    /**
     * @param $name
     * @return self
     */
    public function initializing($name)
    {
        if (!empty($this->pending[$name])) {
            return true;
        }

        $this->pending[$name] = true;

        return false;
    }

    /**
     * @param $name
     * @return object|null
     */
    public function service($name)
    {
        return isset($this->services[$name]) ? $this->services[$name] : null;
    }
}
