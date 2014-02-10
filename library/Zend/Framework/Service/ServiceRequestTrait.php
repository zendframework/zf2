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

trait ServiceRequestTrait
{
    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @param array|callable|FactoryInterface|object|string $factory
     * @return FactoryInterface
     */
    protected function factory($factory)
    {
        if (is_string($factory)) {
            if (is_subclass_of($factory, Factory::class)) {
                return new $factory($this);
            }

            if (is_callable($factory)) {
                return new CallableFactory($this, $factory);
            }

            return new InstanceFactory($this, $factory);
        }

        if (is_object($factory) && $factory instanceof FactoryInterface) {
            return $factory;
        }

        if (is_callable($factory)) {
            return new CallableFactory($this, $factory);
        }

        if (is_array($factory)) {
            return new AbstractFactory($this, $factory);
        }

        return $factory;
    }

    /**
     * @param $name
     * @return false|FactoryInterface
     */
    protected function service($name)
    {
        if (empty($this->services[$name])) {
            return false;
        }

        return $this->services[$name] = $this->factory($this->services[$name]);
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
