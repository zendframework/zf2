<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

trait FactoryTrait
{
    /**
     * @param array|callable|FactoryInterface|object|string $factory
     * @return callable|FactoryInterface
     */
    protected function factory($factory)
    {
        if (is_string($factory) && is_subclass_of($factory, Factory::class)) {
            return new $factory($this);
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

        return new InstanceFactory($this, $factory);
    }
}