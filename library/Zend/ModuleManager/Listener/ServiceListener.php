<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener;

use Traversable;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class ServiceListener implements ListenerAggregateInterface
{
    /**
     * @var bool
     */
    protected $configured = false;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Default service manager used to fulfill other SMs that need to be lazy loaded
     *
     * @var ServiceManager
     */
    protected $defaultServiceManager;

    /**
     * @var array
     */
    protected $defaultServiceConfig;

    /**
     * @var array
     */
    protected $serviceManagers = array();

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager, $configuration = null)
    {
        $this->defaultServiceManager = $serviceManager;
        $this->defaultServiceConfig = $configuration;
    }

    /**
     * @param string $key
     * @param ServiceManager|string $serviceManager
     * @return ServiceListener
     */
    public function addServiceManager($serviceManager, $key, $moduleInterface, $method)
    {
        if (is_string($serviceManager)) {
            $smKey = $serviceManager;
        } elseif ($serviceManager instanceof ServiceManager) {
            $smKey = spl_object_hash($serviceManager);
        } else {
            throw new Exception\RuntimeException(sprintf(
                'Invalid service manager provided, expected ServiceManager or string, %s provided',
                (string) $serviceManager
            ));
        }
        $this->serviceManagers[$smKey] = array(
            'service_manager'        => $serviceManager,
            'config_key'             => $key,
            'module_class_interface' => $moduleInterface,
            'module_class_method'    => $method,
            'configuration'          => array(),
        );
        return $this;
    }

    /**
     * @param  EventManagerInterface $events
     * @return ServiceListener
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, array($this, 'onLoadModule'));
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, array($this, 'onLoadModulesPost'));
        return $this;
    }

    /**
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $key => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * Retrieve service manager configuration from module, and
     * configure the service manager.
     *
     * If the module does not implement ServiceProviderInterface and does not
     * implement the "getServiceConfig()" method, does nothing. Also,
     * if the return value of that method is not a ServiceConfig object,
     * or not an array or Traversable that can seed one, does nothing.
     *
     * @param  ModuleEvent $e
     * @return void
     */
    public function onLoadModule(ModuleEvent $e)
    {
        $module = $e->getModule();

        foreach ($this->serviceManagers as $key => $sm) {

            if (!$module instanceof $sm['module_class_interface']
                && !method_exists($module, $sm['module_class_method'])
            ) {
                continue;
            }

            $config = $module->{$sm['module_class_method']}();

            if ($config instanceof ServiceConfig) {
                $config = $this->serviceConfigToArray($config);
            }

            if ($config instanceof Traversable) {
                $config = ArrayUtils::iteratorToArray($config);
            }

            if (!is_array($config)) {
                // If we don't have an array by this point, nothing left to do.
                continue;
            }

            // We're keeping track of which modules provided which configuration to which serivce managers.
            // The actual merging takes place later. Doing it this way will enable us to provide more powerful
            // debugging tools for showing which modules overrode what.
            $this->serviceManagers[$key]['configuration'][$e->getModuleName() . '::' . $sm['module_class_method'] . '()'] = $config;
        }
    }

    /**
     * Use merged configuration to configure service manager
     *
     * If the merged configuration has a non-empty, array 'service_manager'
     * key, it will be passed to a ServiceManager Config object, and
     * used to configure the service manager.
     *
     * @param  ModuleEvent $e
     * @return void
     */
    public function onLoadModulesPost(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config         = $configListener->getMergedConfig(false);

        if ($this->defaultServiceConfig) {
            $defaultConfig = array('service_manager' => $this->defaultServiceConfig);
        }

        foreach ($this->serviceManagers as $key => $sm) {

            if (isset($defaultConfig[$sm['config_key']])
                && is_array($defaultConfig[$sm['config_key']])
                && !empty($defaultConfig[$sm['config_key']])
            ) {
                $this->serviceManagers[$key]['configuration']['default_config'] = $defaultConfig[$sm['config_key']];
            }

            if (isset($config[$sm['config_key']])
                && is_array($config[$sm['config_key']])
                && !empty($config[$sm['config_key']])
            ) {
                $this->serviceManagers[$key]['configuration']['merged_config'] = $config[$sm['config_key']];
            }

            // Merge all of the things!
            $smConfig = array();
            foreach ($this->serviceManagers[$key]['configuration'] as $configs) {
                if (isset($configs['configuration_classes'])) {
                    foreach ($configs['configuration_classes'] as $class) {
                        $configs = ArrayUtils::merge($configs, $this->serviceConfigToArray($class));
                    }
                }
                $smConfig = ArrayUtils::merge($smConfig, $configs);
            }

            if (!$sm['service_manager'] instanceof ServiceManager) {
                $instance = $this->defaultServiceManager->get($sm['service_manager']);
                if (!$instance instanceof ServiceManager) {
                    throw new Exception\RuntimeException(sprintf(
                        'Could not find a valid ServiceManager for %s',
                        $sm['service_manager']
                    ));
                }
                $sm['service_manager'] = $instance;
            }
            $serviceConfig = new ServiceConfig($smConfig);
            $serviceConfig->configureServiceManager($sm['service_manager']);
        }

        $this->configured = true;
    }

    /**
     * Merge a service configuration container
     *
     * Extracts the various service configuration arrays, and then merges with
     * the internal service configuration.
     *
     * @param  ServiceConfig|string $config Instance of ServiceConfig or class name
     * @return array
     */
    protected function serviceConfigToArray($config)
    {
        if (is_string($config) && class_exists($config)) {
            $class  = $config;
            $config = new $class;
        }

        if (!$config instanceof ServiceConfig) {
            throw new Exception\RuntimeException(sprintf(
                'Invalid service manager configuration class provided; received "%s", expected an instance of Zend\ServiceManager\Config',
                $class
            ));
        }

        return array(
            'abstract_factories' => $config->getAbstractFactories(),
            'aliases'            => $config->getAliases(),
            'initializers'       => $config->getInitializers(),
            'factories'          => $config->getFactories(),
            'invokables'         => $config->getInvokables(),
            'services'           => $config->getServices(),
            'shared'             => $config->getShared(),
        );
    }
}
