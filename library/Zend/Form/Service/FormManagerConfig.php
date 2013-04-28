<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\FormManager\FormManagerAwareInterface;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceManager;

class FormManagerConfig implements ConfigInterface
{
    /**
     * Services that can be instantiated without factories
     *
     * @var array
     */
    protected $invokables = array(
        'FormFactory'        => 'Zend\Form\Factory',
        'InputFilterFactory' => 'Zend\InputFilter\Factory',
    );

    /**
     * Service factories
     *
     * @var array
     */
    protected $factories = array(
        'ElementManager' => 'Zend\Form\Service\FormElementManagerFactory',
    );

    /**
     * Abstract factories
     *
     * @var array
     */
    protected $abstractFactories = array();

    /**
     * Aliases
     *
     * @var array
     */
    protected $aliases = array(
        'FormElementManager' => 'ElementManager',
    );

    /**
     * Shared services
     *
     * Services are shared by default; this is primarily to indicate services
     * that should NOT be shared
     *
     * @var array
     */
    protected $shared = array();

    /**
     * Delegators
     *
     * @var array
     */
    protected $delegators = array();

    /**
     * Constructor
     *
     * Merges internal arrays with those passed via configuration
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['invokables'])) {
            $this->invokables = array_merge($this->invokables, $config['invokables']);
        }

        if (isset($config['factories'])) {
            $this->factories = array_merge($this->factories, $config['factories']);
        }

        if (isset($config['abstract_factories'])) {
            $this->abstractFactories = array_merge($this->abstractFactories, $config['abstract_factories']);
        }

        if (isset($config['aliases'])) {
            $this->aliases = array_merge($this->aliases, $config['aliases']);
        }

        if (isset($config['shared'])) {
            $this->shared = array_merge($this->shared, $config['shared']);
        }

        if (isset($config['delegators'])) {
            $this->delegators = array_merge($this->delegators, $config['delegators']);
        }
    }

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * In addition to using each of the internal properties to configure the
     * service manager, also adds an initializer to inject the <b>Form Manager</b>
     * in objects implementing <i>FormManagerAwareInterface</i>.
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

        foreach ($this->delegators as $originalServiceName => $delegators) {
            foreach ($delegators as $delegator) {
                $serviceManager->addDelegator($originalServiceName, $delegator);
            }
        }

        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if ($instance instanceof FormManagerAwareInterface) {
                $instance->setFormManager($serviceManager);
            }
        });

        $serviceManager->setService('FormManager', $serviceManager);
        $serviceManager->setAlias('Zend\Form\FormManager', 'FormManager');
    }
}
