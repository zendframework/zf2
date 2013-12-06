<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\Exception\RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceRequest;
use Zend\ServiceManager\ServiceListenerInterface as ServiceManagerListenerInterface;
use Zend\EventManager\ListenerAggregateInterface;

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
        'ModuleManager'                  => 'Zend\Mvc\Service\ModuleManagerFactory',
        'ServiceListener'                => 'Zend\Mvc\Service\ServiceListenerFactory',
        'EventManager'                   => 'Zend\Mvc\Service\EventManagerFactory',
        'SharedEventManager'             => 'Zend\Mvc\Service\SharedEventManagerFactory',
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

        'basepath'            => 'Zend\View\Helper\BasePath',
        'cycle'               => 'Zend\View\Helper\Cycle',
        'declarevars'         => 'Zend\View\Helper\DeclareVars',
        'doctype'             => 'Zend\View\Helper\Doctype', // overridden by a factory in ViewHelperManagerFactory
        'escapehtml'          => 'Zend\View\Helper\EscapeHtml',
        'escapehtmlattr'      => 'Zend\View\Helper\EscapeHtmlAttr',
        'escapejs'            => 'Zend\View\Helper\EscapeJs',
        'escapecss'           => 'Zend\View\Helper\EscapeCss',
        'escapeurl'           => 'Zend\View\Helper\EscapeUrl',
        'gravatar'            => 'Zend\View\Helper\Gravatar',
        'headlink'            => 'Zend\View\Helper\HeadLink',
        'headmeta'            => 'Zend\View\Helper\HeadMeta',
        'headscript'          => 'Zend\View\Helper\HeadScript',
        'headstyle'           => 'Zend\View\Helper\HeadStyle',
        'headtitle'           => 'Zend\View\Helper\HeadTitle',
        'htmlflash'           => 'Zend\View\Helper\HtmlFlash',
        'htmllist'            => 'Zend\View\Helper\HtmlList',
        'htmlobject'          => 'Zend\View\Helper\HtmlObject',
        'htmlpage'            => 'Zend\View\Helper\HtmlPage',
        'htmlquicktime'       => 'Zend\View\Helper\HtmlQuicktime',
        'inlinescript'        => 'Zend\View\Helper\InlineScript',
        'json'                => 'Zend\View\Helper\Json',
        'layout'              => 'Zend\View\Helper\Layout',
        'paginationcontrol'   => 'Zend\View\Helper\PaginationControl',
        'partialloop'         => 'Zend\View\Helper\PartialLoop',
        'partial'             => 'Zend\View\Helper\Partial',
        'placeholder'         => 'Zend\View\Helper\Placeholder',
        'renderchildmodel'    => 'Zend\View\Helper\RenderChildModel',
        'rendertoplaceholder' => 'Zend\View\Helper\RenderToPlaceholder',
        'serverurl'           => 'Zend\View\Helper\ServerUrl',
        'url'                 => 'Zend\View\Helper\Url',
        'viewmodel'           => 'Zend\View\Helper\ViewModel',

    );

    /**
     * Create the service listener service
     *
     * Tries to get a service named ServiceListenerInterface from the service
     * locator, otherwise creates a Zend\ModuleManager\Listener\ServiceListener
     * service, passing it the service locator instance and the default service
     * configuration, which can be overridden by modules.
     *
     * It looks for the 'service_listener_options' key in the application
     * config and tries to add service manager as configured. The value of
     * 'service_listener_options' must be a list (array) which contains the
     * following keys:
     *   - service_manager: the name of the service manage to create as string
     *   - config_key: the name of the configuration key to search for as string
     *   - interface: the name of the interface that modules can implement as string
     *   - method: the name of the method that modules have to implement as string
     *
     * @param  ServiceManager  $serviceLocator
     * @return ServiceListener
     * @throws InvalidArgumentException For invalid configurations.
     * @throws RuntimeException
     */
    public function __invoke(ServiceManager $sm)
    {
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

        if (isset($configuration['service_listener_options'])) {
            if (!is_array($configuration['service_listener_options'])) {
                throw new InvalidArgumentException(sprintf(
                    'The value of service_listener_options must be an array, %s given.',
                    gettype($configuration['service_listener_options'])
                ));
            }

            foreach ($configuration['service_listener_options'] as $key => $newServiceManager) {
                if (!isset($newServiceManager['service_manager'])) {
                    throw new InvalidArgumentException(sprintf(self::MISSING_KEY_ERROR, $key, 'service_manager'));
                } elseif (!is_string($newServiceManager['service_manager'])) {
                    throw new InvalidArgumentException(sprintf(
                        self::VALUE_TYPE_ERROR,
                        'service_manager',
                        gettype($newServiceManager['service_manager'])
                    ));
                }
                if (!isset($newServiceManager['config_key'])) {
                    throw new InvalidArgumentException(sprintf(self::MISSING_KEY_ERROR, $key, 'config_key'));
                } elseif (!is_string($newServiceManager['config_key'])) {
                    throw new InvalidArgumentException(sprintf(
                        self::VALUE_TYPE_ERROR,
                        'config_key',
                        gettype($newServiceManager['config_key'])
                    ));
                }
                if (!isset($newServiceManager['interface'])) {
                    throw new InvalidArgumentException(sprintf(self::MISSING_KEY_ERROR, $key, 'interface'));
                } elseif (!is_string($newServiceManager['interface'])) {
                    throw new InvalidArgumentException(sprintf(
                        self::VALUE_TYPE_ERROR,
                        'interface',
                        gettype($newServiceManager['interface'])
                    ));
                }
                if (!isset($newServiceManager['method'])) {
                    throw new InvalidArgumentException(sprintf(self::MISSING_KEY_ERROR, $key, 'method'));
                } elseif (!is_string($newServiceManager['method'])) {
                    throw new InvalidArgumentException(sprintf(
                        self::VALUE_TYPE_ERROR,
                        'method',
                        gettype($newServiceManager['method'])
                    ));
                }

                $serviceListener->addServiceManager(
                    $newServiceManager['service_manager'],
                    $newServiceManager['config_key'],
                    $newServiceManager['interface'],
                    $newServiceManager['method']
                );
            }
        }

        $serviceListener->addServiceManager(
            $sm,
            'service_manager',
            'Zend\ModuleManager\Feature\ServiceProviderInterface',
            'getServiceConfig'
        );

        $serviceListener->addServiceManager(
            'ControllerLoader',
            'controllers',
            'Zend\ModuleManager\Feature\ControllerProviderInterface',
            'getControllerConfig'
        );

        $serviceListener->addServiceManager(
            'ControllerPluginManager',
            'controller_plugins',
            'Zend\ModuleManager\Feature\ControllerPluginProviderInterface',
            'getControllerPluginConfig'
        );

        $serviceListener->addServiceManager(
            'ViewHelperManager',
            'view_helpers',
            'Zend\ModuleManager\Feature\ViewHelperProviderInterface',
            'getViewHelperConfig'
        );

        $serviceListener->addServiceManager(
            'ValidatorManager',
            'validators',
            'Zend\ModuleManager\Feature\ValidatorProviderInterface',
            'getValidatorConfig'
        );

        $serviceListener->addServiceManager(
            'FilterManager',
            'filters',
            'Zend\ModuleManager\Feature\FilterProviderInterface',
            'getFilterConfig'
        );

        $serviceListener->addServiceManager(
            'FormElementManager',
            'form_elements',
            'Zend\ModuleManager\Feature\FormElementProviderInterface',
            'getFormElementConfig'
        );

        $serviceListener->addServiceManager(
            'RoutePluginManager',
            'route_manager',
            'Zend\ModuleManager\Feature\RouteProviderInterface',
            'getRouteConfig'
        );

        $serviceListener->addServiceManager(
            'SerializerAdapterManager',
            'serializers',
            'Zend\ModuleManager\Feature\SerializerProviderInterface',
            'getSerializerConfig'
        );

        $serviceListener->addServiceManager(
            'HydratorManager',
            'hydrators',
            'Zend\ModuleManager\Feature\HydratorProviderInterface',
            'getHydratorConfig'
        );

        $serviceListener->addServiceManager(
            'InputFilterManager',
            'input_filters',
            'Zend\ModuleManager\Feature\InputFilterProviderInterface',
            'getInputFilterConfig'
        );

        return $serviceListener;
    }
}
