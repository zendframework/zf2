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
 * Delegator factory interface
 */
interface DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string                  $name
     * @param  callable                $callback
     * @param  array                   $options
     * @return object
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator, $name, callable $callback, array $options = []);
}