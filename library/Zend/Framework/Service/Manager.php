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
use Zend\Framework\Service\Factory\FactoryInterface;

class Manager
    implements ManagerInterface
{
    /**
     * @var ConfigInterface
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
     * @param ConfigInterface $config
     * @return self
     */
    public function config(ConfigInterface $config)
    {
        $this->service = $config;
        return $this;
    }

    /**
     * @return ConfigInterface
     */
    public function configuration()
    {
        return $this->service;
    }

    /**
     * @param array|callable|FactoryInterface|object|string $factory
     * @return FactoryInterface
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

        if (\is_object($factory) && $factory instanceof FactoryInterface) {
            return $factory;
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
     * @param string $name
     * @param array $options
     * @param bool $shared
     * @return false|object
     */
    public function get($name, array $options = [], $shared = true)
    {
        return $this->__invoke(new Request($name, $shared), $options);
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
     * @return false|FactoryInterface
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
    public function __invoke(RequestInterface $request, array $options = [])
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
