<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Service\DiStrictAbstractServiceFactory;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\Config as ServiceManagerConfig;

class ControllerLoaderFactory implements FactoryInterface
{
    /**
     * Create the controller loader service
     *
     * Creates and returns an instance of ControllerManager. The
     * only controllers this manager will allow are those defined in the
     * application configuration's "controllers" array. If a controller is
     * matched, the scoped manager will attempt to load the controller.
     * Finally, it will attempt to inject the controller plugin manager
     * if the controller implements a setPluginManager() method.
     *
     * This plugin manager is _not_ peered against DI, and as such, will
     * not load unknown classes.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerManager
     */
    public function createService(ServiceManager $serviceLocator)
    {
        $config = $serviceLocator->get(new ServiceRequest('ApplicationConfig'));

        $controllerLoader = new ControllerManager(new ServiceManagerConfig($config['controllers']));
        $controllerLoader->setServiceLocator($serviceLocator);
        //$controllerLoader->addPeeringServiceManager($serviceLocator);

        //if (isset($config['di']) && isset($config['di']['allowed_controllers']) && $serviceLocator->has('Di')) {
            //$controllerLoader->addAbstractFactory($serviceLocator->get('DiStrictAbstractServiceFactory'));
        //}

        return $controllerLoader;
    }

    public function __invoke(ServiceManager $sm)
    {
        return $this->createService($sm);
    }
}
