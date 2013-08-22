<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Factory;

use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a hydrator plugin manager
 *
 * Users can define their own hydrators by defining the "hydrators" key in their
 * config file
 */
class HydratorPluginManagerFactory implements FactoryInterface
{
    /**
     * Create a hydrator plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return HydratorPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['hydrators']) ? $config['hydrators'] : array();

        return new HydratorPluginManager($config);
    }
}
