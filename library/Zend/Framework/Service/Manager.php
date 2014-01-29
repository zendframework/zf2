<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Exception;
use Zend\Framework\Service\Factory\AbstractFactory;
use Zend\Framework\Service\Factory\CallableFactory;
use Zend\Framework\Service\Factory\InstanceFactory;
use Zend\Framework\Service\Factory\Factory;

class Manager
    implements ManagerInterface
{
    /**
     * @var array
     */
    protected $service = [];

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $pending = [];

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
     * @param array $config
     * @return self
     */
    public function config(array $config)
    {
        $this->service = $config;
        return $this;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function configure($name, $class)
    {
        $this->service[$name] = $class;
    }

    /**
     * @param string|callable $factory
     * @return Factory
     */
    public function factory($factory)
    {
        if (\is_string($factory)) {
            if (\is_subclass_of($factory, Factory::class)) {
                return new $factory($this);
            }

            if (\is_callable($factory)) {
                return new CallableFactory($this, $factory);
            }

            return new InstanceFactory($this, $factory);
        }

        if (\is_callable($factory)) {
            return new CallableFactory($this, $factory);
        }

        if (\is_array($factory)) {
            return new AbstractFactory($this, $factory);
        }

        return $factory;
    }

    /**
     * @param $name
     * @param array $options
     * @return bool|object
     */
    public function get($name, array $options = [])
    {
        return $this->request(new Request($name), $options);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->shared[$name]);
    }

    /**
     * @param $name
     * @return bool|Factory|callable
     */
    public function service($name)
    {
        if (empty($this->service[$name])) {
            return false;
        }

        return $this->service[$name] = $this->factory($this->service[$name]);
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return bool|object
     * @throws Exception
     */
    public function request(RequestInterface $request, array $options = [])
    {
        $name = $request->alias();

        if ($request->shared() && isset($this->shared[$name])) {
            return $this->shared[$name];
        }

        if (!empty($this->pending[$name])) {
            throw new Exception('Circular dependency: '.$name);
        }

        $this->pending[$name] = true;

        $instance = false;

        $service = $this->service($name);

        if ($service) {
            $instance = $service->service($request, $options);
        }

        if ($request->shared()) {
            $this->shared[$name] = $instance;
        }

        $this->pending[$name] = false;

        return $instance;
    }
}
