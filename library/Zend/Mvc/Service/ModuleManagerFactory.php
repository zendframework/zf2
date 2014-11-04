<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Filter\FilterPluginManager;
use Zend\Form\FormElementManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\Log\ProcessorPluginManager;
use Zend\Log\WriterPluginManager;
use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\Serializer\AdapterPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\HydratorPluginManager;
use Zend\Validator\ValidatorPluginManager;
use Zend\View\HelperPluginManager;

class ModuleManagerFactory implements FactoryInterface
{
    /**
     * Creates and returns the module manager
     *
     * Instantiates the default module listeners, providing them configuration
     * from the "module_listener_options" key of the ApplicationConfig
     * service. Also sets the default config glob path.
     *
     * Module manager is instantiated and provided with an EventManager, to which
     * the default listener aggregate is attached. The ModuleEvent is also created
     * and attached to the module manager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ModuleManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('ServiceListener')) {
            $serviceLocator->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');
        }

        $configuration    = $serviceLocator->get('ApplicationConfig');
        $listenerOptions  = new ListenerOptions($configuration['module_listener_options']);
        $defaultListeners = new DefaultListenerAggregate($listenerOptions);
        $serviceListener  = $serviceLocator->get('ServiceListener');

        $serviceListener->addServiceManager(
            $serviceLocator,
            ServiceManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\ServiceProviderInterface',
            'getServiceConfig'
        );
        $serviceListener->addServiceManager(
            'ControllerLoader',
            ControllerManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\ControllerProviderInterface',
            'getControllerConfig'
        );
        $serviceListener->addServiceManager(
            'ControllerPluginManager',
            PluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\ControllerPluginProviderInterface',
            'getControllerPluginConfig'
        );
        $serviceListener->addServiceManager(
            'ViewHelperManager',
            HelperPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\ViewHelperProviderInterface',
            'getViewHelperConfig'
        );
        $serviceListener->addServiceManager(
            'ValidatorManager',
            ValidatorPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\ValidatorProviderInterface',
            'getValidatorConfig'
        );
        $serviceListener->addServiceManager(
            'FilterManager',
            FilterPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\FilterProviderInterface',
            'getFilterConfig'
        );
        $serviceListener->addServiceManager(
            'FormElementManager',
            FormElementManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\FormElementProviderInterface',
            'getFormElementConfig'
        );
        $serviceListener->addServiceManager(
            'RoutePluginManager',
            RoutePluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\RouteProviderInterface',
            'getRouteConfig'
        );
        $serviceListener->addServiceManager(
            'SerializerAdapterManager',
            AdapterPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\SerializerProviderInterface',
            'getSerializerConfig'
        );
        $serviceListener->addServiceManager(
            'HydratorManager',
            HydratorPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\HydratorProviderInterface',
            'getHydratorConfig'
        );
        $serviceListener->addServiceManager(
            'InputFilterManager',
            InputFilterPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\InputFilterProviderInterface',
            'getInputFilterConfig'
        );
        $serviceListener->addServiceManager(
            'LogProcessorManager',
            ProcessorPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\LogProcessorProviderInterface',
            'getLogProcessorConfig'
        );
        $serviceListener->addServiceManager(
            'LogWriterManager',
            WriterPluginManager::CONFIGURATION,
            'Zend\ModuleManager\Feature\LogWriterProviderInterface',
            'getLogWriterConfig'
        );

        $events = $serviceLocator->get('EventManager');
        $events->attach($defaultListeners);
        $events->attach($serviceListener);

        $moduleEvent = new ModuleEvent;
        $moduleEvent->setParam('ServiceManager', $serviceLocator);

        $moduleManager = new ModuleManager($configuration['modules'], $events);
        $moduleManager->setEvent($moduleEvent);

        return $moduleManager;
    }
}
