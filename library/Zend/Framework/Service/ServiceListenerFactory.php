<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\ServiceListenerInterface as ServiceManagerListenerInterface;
use Zend\Framework\EventManager\ListenerAggregateInterface;

class ServiceListenerFactory implements ServiceManagerListenerInterface
{
    /**
     * @var string
     */
    const MISSING_KEY_ERROR = 'Invalid service listener options detected, %s array must contain %s key.';

    /**
     * @var string
     */
    const VALUE_TYPE_ERROR = 'Invalid service listener options detected, %s must be a string, %s given.';

    /**
     * Default mvc-related service configuration -- can be overridden by modules.
     *
     * @var array
     */
    protected $defaultServiceConfig = array(
        /*'invokables' => array(
            'DispatchListener'     => 'Zend\Mvc\DispatchListener',
            'RouteListener'        => 'Zend\Mvc\RouteListener',
            'SendResponseListener' => 'Zend\Mvc\SendResponseListener'
        ),
        'factories' => array(
            'Application'                    => 'Zend\Mvc\Service\ApplicationFactory',
            'Config'                         => 'Zend\Mvc\Service\ConfigFactory',
            'ControllerLoader'               => 'Zend\Mvc\Service\ControllerLoaderFactory',
            'ControllerPluginManager'        => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
            'ConsoleAdapter'                 => 'Zend\Mvc\Service\ConsoleAdapterFactory',
            'ConsoleRouter'                  => 'Zend\Mvc\Service\RouterFactory',
            'ConsoleViewManager'             => 'Zend\Mvc\Service\ConsoleViewManagerFactory',
            'DependencyInjector'             => 'Zend\Mvc\Service\DiFactory',
            'DiAbstractServiceFactory'       => 'Zend\Mvc\Service\DiAbstractServiceFactoryFactory',
            'DiServiceInitializer'           => 'Zend\Mvc\Service\DiServiceInitializerFactory',
            'DiStrictAbstractServiceFactory' => 'Zend\Mvc\Service\DiStrictAbstractServiceFactoryFactory',
            'FilterManager'                  => 'Zend\Mvc\Service\FilterManagerFactory',
            'FormElementManager'             => 'Zend\Mvc\Service\FormElementManagerFactory',
            'HttpRouter'                     => 'Zend\Mvc\Service\RouterFactory',
            'HttpViewManager'                => 'Zend\Mvc\Service\HttpViewManagerFactory',
            'HydratorManager'                => 'Zend\Mvc\Service\HydratorManagerFactory',
            'InputFilterManager'             => 'Zend\Mvc\Service\InputFilterManagerFactory',
            'MvcTranslator'                  => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'PaginatorPluginManager'         => 'Zend\Mvc\Service\PaginatorPluginManagerFactory',
            'Request'                        => 'Zend\Mvc\Service\RequestFactory',
            'Response'                       => 'Zend\Mvc\Service\ResponseFactory',
            'Router'                         => 'Zend\Mvc\Service\RouterFactory',
            'RoutePluginManager'             => 'Zend\Mvc\Service\RoutePluginManagerFactory',
            'SerializerAdapterManager'       => 'Zend\Mvc\Service\SerializerAdapterPluginManagerFactory',
            'ValidatorManager'               => 'Zend\Mvc\Service\ValidatorManagerFactory',
            'ViewHelperManager'              => 'Zend\Mvc\Service\ViewHelperManagerFactory',
            'ViewFeedRenderer'               => 'Zend\Mvc\Service\ViewFeedRendererFactory',
            'ViewFeedStrategy'               => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
            'ViewJsonRenderer'               => 'Zend\Mvc\Service\ViewJsonRendererFactory',
            'ViewJsonStrategy'               => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
            'ViewManager'                    => 'Zend\Mvc\Service\ViewManagerFactory',
            'ViewResolver'                   => 'Zend\Mvc\Service\ViewResolverFactory',
            'ViewTemplateMapResolver'        => 'Zend\Mvc\Service\ViewTemplateMapResolverFactory',
            'ViewTemplatePathStack'          => 'Zend\Mvc\Service\ViewTemplatePathStackFactory',
        ),
        'aliases' => array(
            'Configuration'                          => 'Config',
            'Console'                                => 'ConsoleAdapter',
            'Di'                                     => 'DependencyInjector',
            'Zend\Di\LocatorInterface'               => 'DependencyInjector',
            'Zend\Mvc\Controller\PluginManager'      => 'ControllerPluginManager',
            'Zend\View\Resolver\TemplateMapResolver' => 'ViewTemplateMapResolver',
            'Zend\View\Resolver\TemplatePathStack'   => 'ViewTemplatePathStack',
            'Zend\View\Resolver\AggregateResolver'   => 'ViewResolver',
            'Zend\View\Resolver\ResolverInterface'   => 'ViewResolver',
        ),
        'abstract_factories' => array(
            'Zend\Form\FormAbstractServiceFactory',
        ),*/
        'ModuleManager'                  => 'Zend\Framework\Service\ModuleManagerFactory',
        'ServiceListener'                => 'Zend\Framework\Service\ServiceListenerFactory',
        'EventManager'                   => 'Zend\Framework\Service\EventManagerFactory',
        'SharedEventManager'             => 'Zend\Framework\Service\SharedEventManagerFactory',
        'ModuleManager\DefaultListeners' => 'Zend\ModuleManager\Listener\DefaultListenersFactory',

        'DispatchListener'    => 'Zend\Framework\Dispatch\Listener',
        'RouteListener'       => 'Zend\Framework\Route\Listener',
        'ModuleRouteListener' => 'Zend\Framework\Module\RouteListener',
        'ResponseListener'    => 'Zend\Framework\Response\Listener',

        'Application'                    => 'Zend\Framework\ApplicationFactory',
        'Config'                         => 'Zend\Mvc\Service\ConfigFactory',
        'ControllerLoader'               => 'Zend\Mvc\Service\ControllerLoaderFactory',
        'ControllerPluginManager'        => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
        'ConsoleAdapter'                 => 'Zend\Mvc\Service\ConsoleAdapterFactory',
        'ConsoleRouter'                  => 'Zend\Mvc\Service\RouterFactory',
        'ConsoleViewManager'             => 'Zend\Mvc\Service\ConsoleViewManagerFactory',
        'DependencyInjector'             => 'Zend\Mvc\Service\DiFactory',
        'DiAbstractServiceFactory'       => 'Zend\Mvc\Service\DiAbstractServiceFactoryFactory',
        'DiServiceInitializer'           => 'Zend\Mvc\Service\DiServiceInitializerFactory',
        'DiStrictAbstractServiceFactory' => 'Zend\Mvc\Service\DiStrictAbstractServiceFactoryFactory',
        'FilterManager'                  => 'Zend\Mvc\Service\FilterManagerFactory',
        'FormElementManager'             => 'Zend\Mvc\Service\FormElementManagerFactory',
        'HttpRouter'                     => 'Zend\Mvc\Service\RouterFactory',
        'HttpViewManager'                => 'Zend\Mvc\Service\HttpViewManagerFactory',
        'HydratorManager'                => 'Zend\Mvc\Service\HydratorManagerFactory',
        'InputFilterManager'             => 'Zend\Mvc\Service\InputFilterManagerFactory',
        'MvcTranslator'                  => 'Zend\Mvc\Service\TranslatorServiceFactory',
        'PaginatorPluginManager'         => 'Zend\Mvc\Service\PaginatorPluginManagerFactory',
        'Request'                        => 'Zend\Mvc\Service\RequestFactory',
        'Response'                       => 'Zend\Mvc\Service\ResponseFactory',
        'Router'                         => 'Zend\Mvc\Service\RouterFactory',
        'RoutePluginManager'             => 'Zend\Mvc\Service\RoutePluginManagerFactory',
        'SerializerAdapterManager'       => 'Zend\Mvc\Service\SerializerAdapterPluginManagerFactory',
        'ValidatorManager'               => 'Zend\Mvc\Service\ValidatorManagerFactory',
        'ViewHelperManager'              => 'Zend\Mvc\Service\ViewHelperManagerFactory',
        'ViewFeedRenderer'               => 'Zend\Mvc\Service\ViewFeedRendererFactory',
        'ViewFeedStrategy'               => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
        'ViewJsonRenderer'               => 'Zend\Mvc\Service\ViewJsonRendererFactory',
        'ViewJsonStrategy'               => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
        'ViewManager'                    => 'Zend\Mvc\Service\ViewManagerFactory',
        'ViewResolver'                   => 'Zend\Mvc\Service\ViewResolverFactory',
        'ViewTemplateMapResolver'        => 'Zend\Mvc\Service\ViewTemplateMapResolverFactory',
        'ViewTemplatePathStack'          => 'Zend\Mvc\Service\ViewTemplatePathStackFactory',
    );

    public function __invoke(ServiceManager $sm)
    {var_dump(__FILE__);
        $configuration   = $sm->get(new ServiceRequest('ApplicationConfig'));

        if ($sm->has('ServiceListenerInterface')) {
            $serviceListener = $sm->get(new ServiceRequest('ServiceListenerInterface'));

            if (!$serviceListener instanceof ServiceListenerInterface) {
                throw new RuntimeException(
                    'The service named ServiceListenerInterface must implement ' .
                    'Zend\ModuleManager\Listener\ServiceListenerInterface'
                );
            }

            $serviceListener->setDefaultServiceConfig($this->defaultServiceConfig);
        } else {
            $serviceListener = new ServiceListener($sm, $this->defaultServiceConfig);
        }

        return $serviceListener;
    }
}
