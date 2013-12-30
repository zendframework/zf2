<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use ReflectionClass;
use Zend\Framework\EventManager\ListenerTrait as Listener;

trait ListenerTrait
{
    /**
     *
     */
    use Listener, ServicesTrait;

    /**
     * @var ListenerConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->config->add($name, $class);
    }

    /**
     * @param $name
     * @return object
     */
    public function get($name)
    {
        return $this->__invoke(new Event($name));
    }

    /**
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->config->get($name);
    }

    /**
     * @param ListenerConfigInterface $config
     * @return $this
     */
    public function setConfig(ListenerConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->shared[$name] = $service;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->shared[$name]);
    }
}
