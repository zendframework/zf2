<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceRequestInterface;
use Zend\ServiceManager\Zf2Compat\ServiceNameNormalizerAbstractFactory;

/**
 * Plugin manager implementation for paginator adapters.
 *
 * Enforces that adapters retrieved are instances of
 * Adapter\AdapterInterface. Additionally, it registers a number of default
 * adapters available.
 */
class AdapterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of adapters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'array'         => 'Zend\Paginator\Adapter\ArrayAdapter',
        'iterator'      => 'Zend\Paginator\Adapter\Iterator',
        'null'          => 'Zend\Paginator\Adapter\Null',
    );

    /**
     * Default set of adapter factories
     *
     * @var array
     */
    protected $factories = array(
        'dbselect'         => 'Zend\Paginator\Adapter\Service\DbSelectFactory'
    );

    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addAbstractFactory(new ServiceNameNormalizerAbstractFactory($this), false);
    }

    /**
     * {@inheritDoc}
     */
    protected function createFromFactory($serviceRequest)
    {
        $name    = (string) $serviceRequest;
        $factory = $this->factories[$name];

        if (is_string($factory) && class_exists($factory, true)) {
            if ($serviceRequest instanceof ServiceRequestInterface) {
                $factory = new $factory($serviceRequest->getOptions());
            } else {
                $factory = new $factory();
            }

            $this->factories[$name] = $factory;
        }

        return parent::createFromFactory($serviceRequest);
    }

    /**
     * Validate the plugin
     *
     * Checks that the adapter loaded is an instance
     * of Adapter\AdapterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Adapter\AdapterInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Adapter\AdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
