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
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;

class FooAliasedAbstractFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $originalName = null)
    {
        if ($requestedName == 'app\foo' || $originalName == 'app\foo') {
            return true;
        }
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $originalName = null)
    {
        if ($requestedName == 'app\foo' || $originalName == 'app\foo') {
            return new Foo;
        } else {
            throw new ServiceNotCreatedException("I don't know how to create service $requestedName");
        }
    }
}
