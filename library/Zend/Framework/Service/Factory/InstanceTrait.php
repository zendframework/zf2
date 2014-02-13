<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Service\RequestInterface;

trait InstanceTrait
{
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

        return false;
    }

    /**
     * @param RequestInterface $request
     * @param array|callable|FactoryInterface|object|string $service
     * @param array $options
     * @return mixed
     */
    protected function instance(RequestInterface $request, $service, array $options = [])
    {
        $factory = $this->factory($service);

        return $factory ? $factory->__invoke($request, $options) : false;
    }
}
