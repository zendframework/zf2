<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Framework\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\Config as ServiceManagerConfig;
use Zend\Framework\ServiceManager\ServiceRequest;

abstract class AbstractPluginManagerFactory implements FactoryInterface
{
    const PLUGIN_MANAGER_CLASS = 'AbstractPluginManager';

    /**
     * Create and return a plugin manager.
     * Classes that extend this should provide a valid class for
     * the PLUGIN_MANGER_CLASS constant.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\ServiceManager\AbstractPluginManager
     */
    public function createService(ServiceManager $serviceLocator)
    {
        $configuration = $serviceLocator->get(new ServiceRequest('ApplicationConfig'));

        $pluginManagerClass = static::PLUGIN_MANAGER_CLASS;

        /* @var $plugins \Zend\ServiceManager\AbstractPluginManager */
        $plugins = new $pluginManagerClass(new ServiceManagerConfig($configuration['plugins']));
        $plugins->setServiceLocator($serviceLocator);


        if (isset($configuration['di']) && $serviceLocator->has('Di')) {
            $plugins->addAbstractFactory($serviceLocator->get('DiAbstractServiceFactory'));
        }

        return $plugins;
    }

    public function __invoke(ServiceManager $serviceLocator)
    {
        return $this->createService($serviceLocator);
    }
}
