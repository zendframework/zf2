<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Zend\ServiceManager\Exception\InvalidFactoryException;

/**
 * Service Manager
 */
class ServiceManager implements ServiceLocatorInterface
{
    /**
     * A list of factories (either as string name or callable)
     *
     * @var string[]|callable[]
     */
    protected $factories = [];

    /**
     * @var AbstractFactoryInterface[]
     */
    protected $abstractFactories = [];

    /**
     * @var string[]|DelegatorFactoryInterface[]
     */
    protected $delegators = [];

    /**
     * A list of already loaded services (this act as a local cache)
     *
     * @var array
     */
    protected $services = [];

    /**
     * Should the services be shared by default?
     *
     * @var bool
     */
    protected $sharedByDefault = true;

    /**
     * Allow to activate/deactivate shared per service name
     *
     * Example configuration:
     *
     * 'shared' => [
     *     MyService::class => true, // will be shared, even if "sharedByDefault" is false
     *     MyOtherService::class => false // won't be shared, even if "sharedByDefault" is true
     * ]
     *
     * @var array
     */
    protected $shared = [];

    /**
     * @var ServiceLocatorInterface
     */
    protected $creationContext;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->creationContext = $this;

        if (!empty($config)) {
            $this->configure($config);
        }
    }

    /**
     * Configure the service manager
     *
     * Note that configuration never overwrite previous configuration, but is merged with it. If you need
     * to clear a service manager configuration, you need to create a different service manager
     *
     * Valid top keys are:
     *
     *      - factories: a list of key value that map a service name with a factory
     *      - abstract_factories: a list of object or string of abstract factories
     *      - shared: a list of key value that map a service name to a boolean
     *      - shared_by_default: boolean
     *
     * @param  array $config
     * @return void
     */
    public function configure(array $config)
    {
        // @TODO: for PHP7, we will be able to use coalesce operator

        $this->factories = array_merge($this->factories, isset($config['factories']) ? $config['factories'] : []);
        $this->shared    = array_merge($this->shared, isset($config['shared']) ? $config['shared'] : []);

        $this->sharedByDefault = isset($config['shared_by_default']) ? $config['shared_by_default'] : $this->sharedByDefault;

        // For abstract factories, we always directly instantiate them to avoid checks during construction
        if (isset($config['abstract_factories'])) {
            foreach ($config['abstract_factories'] as $abstractFactory) {
                $this->abstractFactories[] = is_string($abstractFactory) ? new $abstractFactory() : $abstractFactory;
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * This is a highly performance sensitive method, do not modify if you have not benchmarked it carefully
     */
    public function get($name, array $options = [])
    {
        // We start by checking if the service is cached (this is the fastest method)
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        // Let's create the service by fetching the factory
        $factory = $this->getFactory($name);

        if (!isset($this->delegators[$name])) {
            $object = $factory($this->creationContext, $name, $options);
        } else {
            $object = $this->createDelegatorFromFactory($name, $options);
        }

        if (($this->sharedByDefault && !isset($this->shared[$name]))
            || (isset($this->shared[$name]) && $this->shared[$name])) {
            $this->services[$name] = $object;
        }

        return $object;
    }

    /**
     * {@inheritDoc}
     */
    public function has($name, $checkAbstractFactories = false)
    {
        $found = isset($this->services[$name]) || isset($this->factories[$name]);

        if ($found || !$checkAbstractFactories) {
            return $found;
        }

        // Check abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canCreateServiceWithName($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a factory for the given service name
     *
     * @param  string $name
     * @return callable
     * @throws InvalidFactoryException
     */
    protected function getFactory($name)
    {
        $factory = isset($this->factories[$name]) ? $this->factories[$name] : null;

        if (is_callable($factory)) {
            return $factory;
        }

        if (is_string($factory)) {
            $this->factories[$name] = $factory = new $factory();
            return $factory;
        }

        // Check abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canCreateServiceWithName($name)) {
                return $abstractFactory;
            }
        }

        throw new InvalidFactoryException(sprintf(
            'An invalid or missing factory was given for creating service "%s". Did you make sure you added the service
             into the service manager configuration?',
            $name
        ));
    }

    /**
     * @param  string $name
     * @param  array  $options
     * @return object
     */
    protected function createDelegatorFromFactory($name, array $options = [])
    {
        $delegatorsCount  = count($this->delegators[$name]);
        $creationCallback = function() use ($name, $options) {
            return $this->get($name, $options);
        };

        for ($i = 0 ; $i < $delegatorsCount ; $i += 1) {
            $delegatorFactory = $this->delegators[$name][$i];

            if (is_string($delegatorFactory)) {
                $delegatorFactory = $this->delegators[$name][$i] = new $delegatorFactory();
            }

            $creationCallback = function () use ($delegatorFactory, $name, $creationCallback) {
                return $delegatorFactory($this->creationContext, $name, $creationCallback);
            };
        }
    }
}