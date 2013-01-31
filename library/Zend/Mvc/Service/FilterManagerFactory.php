<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\Filter\FilterPluginManager';

    /**
     * Create and return the filter plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Filter\FilterPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceListener \Zend\ModuleManager\Listener\ServiceListener */
        $serviceListener = $serviceLocator->get('ServiceListener');

        // This will allow to register new filters easily, either by implementing the FilterProviderInterface
        // in your Module.php file, or by adding the "filters" key in your module.config.php file
        $serviceListener->addServiceManager(
            'FilterManager',
            'filters',
            'Zend\ModuleManager\Feature\FilterProviderInterface',
            'getFilterConfig'
        );

        return parent::createService($serviceLocator);
    }
}
