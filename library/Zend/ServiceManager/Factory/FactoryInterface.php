<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Interface for a factory
 *
 * A factory is an callable object that is able to create an object. It is given the instance of
 * the service locator, the requested name of the class you want to create and optional options
 */
interface FactoryInterface 
{
    /**
     * Create an object
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string                  $className
     * @param  array                   $options
     * @return object
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator, $className, array $options = []);
}