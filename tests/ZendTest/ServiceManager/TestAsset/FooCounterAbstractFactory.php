<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract factory that keeps track of the number of times it is instantiated
 */
class FooCounterAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var int
     */
    public static $instantiationCount = 0;

    /**
     * Increments instantiation count
     */
    public function __construct()
    {
        static::$instantiationCount += 1;
    }

    /**
     * {@inheritDoc}
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param $name
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name)
    {
        if ($name == 'foo') {
            return true;
        }
    }

    /**
     * {@inheritDoc}
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param $name
     * @return mixed|\ZendTest\ServiceManager\TestAsset\Foo
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name)
    {
        return new Foo;
    }
}
