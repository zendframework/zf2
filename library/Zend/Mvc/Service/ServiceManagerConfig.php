<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ServiceManagerConfig implements ConfigInterface
{
    /**
     * Services that can be instantiated without factories
     *
     * @var array
     */
    protected $invokables = array(
        'SharedEventManager' => 'Zend\EventManager\SharedEventManager',
        'DispatchListener'     => 'Zend\Mvc\DispatchListener',
        'RouteListener'        => 'Zend\Mvc\RouteListener',
        'SendResponseListener' => 'Zend\Mvc\SendResponseListener',

        // View managers
        'HttpViewManager' => 'Zend\Mvc\View\Http\ViewManager'
    );

    /**
     * Service factories
     *
     * @var array
     */
    protected $factories = array(
        'Config'        => 'Zend\Mvc\Service\ConfigFactory',
        'Router'        => 'Zend\Mvc\Service\RouterFactory',
        'Request'       => 'Zend\Mvc\Service\RequestFactory',
        'Response'      => 'Zend\Mvc\Service\ResponseFactory',
        'Application'   => 'Zend\Mvc\Service\ApplicationFactory',
        'ModuleManager' => 'Zend\Mvc\Service\ModuleManagerFactory',
        'Zend\EventManager\EventManagerInterface' => 'Zend\Mvc\Service\EventManagerFactory',

        // View related stuff
        'ViewFeedRenderer' => 'Zend\Mvc\Service\ViewFeedRendererFactory',
        'ViewFeedStrategy' => 'Zend\Mvc\Service\ViewFeedStrategyFactory',
        'ViewJsonRenderer' => 'Zend\Mvc\Service\ViewJsonRendererFactory',
        'ViewJsonStrategy' => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
        'ViewManager' => 'Zend\Mvc\Service\ViewManagerFactory',
        'ViewResolver' => 'Zend\Mvc\Service\ViewResolverFactory',
        'ViewTemplateMapResolver' => 'Zend\Mvc\Service\ViewTemplateMapResolverFactory',
        'ViewTemplatePathStack' => 'Zend\Mvc\Service\ViewTemplatePathStackFactory',
    );

    /**
     * Abstract factories
     *
     * @var array
     */
    protected $abstractFactories = array(
        'Zend\ServiceManager\PluginManagerFactory',
    );

    /**
     * Aliases
     *
     * @var array
     */
    protected $aliases = array(
        'ViewHelperManager'  => 'Zend\View\HelperPluginManager',
        'RoutePluginManager' => 'Zend\Mvc\Router\RoutePluginManager',
        'ControllerManager'  => 'Zend\Mvc\Controller\ControllerManager',
        'ControllerPluginManager' => 'Zend\Mvc\Controller\PluginManager',
        'OptionsManager' => 'Zend\Stdlib\OptionsManager',

        'EventManager' => 'Zend\EventManager\EventManagerInterface'
    );

    /**
     * Shared services
     *
     * Services are shared by default; this is primarily to indicate services
     * that should NOT be shared
     *
     * @var array
     */
    protected $shared = array(
        'Zend\EventManager\EventManagerInterface' => false,
    );

    /**
     * Constructor
     *
     * Merges internal arrays with those passed via configuration
     *
     * @param  array $configuration
     */
    public function __construct(array $configuration = array())
    {
        if (isset($configuration['invokables'])) {
            $this->invokables = array_merge($this->invokables, $configuration['invokables']);
        }

        if (isset($configuration['factories'])) {
            $this->factories = array_merge($this->factories, $configuration['factories']);
        }

        if (isset($configuration['abstract_factories'])) {
            $this->abstractFactories = array_merge($this->abstractFactories, $configuration['abstract_factories']);
        }

        if (isset($configuration['aliases'])) {
            $this->aliases = array_merge($this->aliases, $configuration['aliases']);
        }

        if (isset($configuration['shared'])) {
            $this->shared = array_merge($this->shared, $configuration['shared']);
        }

    }

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * In addition to using each of the internal properties to configure the
     * service manager, also adds an initializer to inject ServiceManagerAware
     * and ServiceLocatorAware classes with the service manager.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->invokables as $name => $class) {
            $serviceManager->setInvokableClass($name, $class);
        }

        foreach ($this->factories as $name => $factoryClass) {
            $serviceManager->setFactory($name, $factoryClass);
        }

        foreach ($this->abstractFactories as $factoryClass) {
            $serviceManager->addAbstractFactory($factoryClass);
        }

        foreach ($this->aliases as $name => $service) {
            $serviceManager->setAlias($name, $service);
        }

        foreach ($this->shared as $name => $value) {
            $serviceManager->setShared($name, $value);
        }

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof EventManagerAwareInterface) {
                if ($instance->getEventManager() instanceof EventManagerInterface) {
                    $instance->getEventManager()->setSharedManager(
                        $serviceManager->get('SharedEventManager')
                    );
                } else {
                    $instance->setEventManager($serviceManager->get('EventManager'));
                }
            }
        });

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($serviceManager);
            }
        });

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof ServiceLocatorAwareInterface) {
                $instance->setServiceLocator($serviceManager);
            }
        });

        $serviceManager->setService('ServiceManager', $serviceManager);
        $serviceManager->setAlias('Zend\ServiceManager\ServiceLocatorInterface', 'ServiceManager');
        $serviceManager->setAlias('Zend\ServiceManager\ServiceManager', 'ServiceManager');
    }
}
