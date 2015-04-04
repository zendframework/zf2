<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Exception;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Initializer\InitializerInterface;

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
     * @var InitializerInterface[]
     */
    protected $initializers = [];

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
    public function __construct(array $config)
    {
        $this->creationContext = $this;
        $this->configure($config);
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

        try {
            if (!isset($this->delegators[$name])) {
                $object = $factory($this->creationContext, $name, $options);
            } else {
                $object = $this->createDelegatorFromFactory($name, $options);
            }
        } catch (Exception $exception) {
            throw new ServiceNotCreatedException(sprintf(
                'Service with name "%s" could not be created. Reason: %s',
                $name,
                $exception->getMessage()
            ));
        }

        foreach ($this->initializers as $initializer) {
            $initializer($this->creationContext, $object);
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
     * @throws ServiceNotFoundException
     */
    protected function getFactory($name)
    {
        $factory = isset($this->factories[$name]) ? $this->factories[$name] : null;

        if (is_string($factory)) {
            $this->factories[$name] = $factory = new $factory();
        }

        if (is_callable($factory)) {
            return $factory;
        }

        // Check abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canCreateServiceWithName($name)) {
                return $abstractFactory;
            }
        }

        throw new ServiceNotFoundException(sprintf(
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
            // Code is inlined for performance reason, instead of abstracting the creation
            $factory = $this->getFactory($name);
            return $factory($this->creationContext, $name, $options);
        };

        for ($i = 0 ; $i < $delegatorsCount ; ++$i) {
            $delegatorFactory = $this->delegators[$name][$i];

            if (is_string($delegatorFactory)) {
                $delegatorFactory = $this->delegators[$name][$i] = new $delegatorFactory();
            }

            $creationCallback = function() use ($delegatorFactory, $name, $creationCallback, $options) {
                return $delegatorFactory($this->creationContext, $name, $creationCallback, $options);
            };
        }

        return $creationCallback($this->creationContext, $name, $creationCallback, $options);
    }

    /**
     * Configure the service manager
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
    protected function configure(array $config)
    {
        $this->factories       = isset($config['factories']) ? $config['factories'] : [];
        $this->delegators      = isset($config['delegators']) ? $config['delegators'] : [];
        $this->shared          = isset($config['shared']) ? $config['shared'] : [];
        $this->sharedByDefault = isset($config['shared_by_default']) ? $config['shared_by_default'] : $this->sharedByDefault;

        // For abstract factories and initializers, we always directly instantiate them to avoid checks during construction
        if (isset($config['abstract_factories'])) {
            foreach ($config['abstract_factories'] as $abstractFactory) {
                if (is_string($abstractFactory)) {
                    $abstractFactory = new $abstractFactory();
                }

                if (is_callable($abstractFactory)) {
                    $this->abstractFactories[] = $abstractFactory;
                    continue;
                }

                throw new InvalidArgumentException(sprintf(
                    'An invalid abstract factory was registered. A callable or an instance of "%s" was expected, but
                    "%s" was received',
                    AbstractFactoryInterface::class,
                    is_object($abstractFactory) ? get_class($abstractFactory) : gettype($abstractFactory)
                ));
            }
        }

        if (isset($config['initializers'])) {
            foreach ($config['initializers'] as $initializer) {
                if (is_string($initializer)) {
                    $initializer = new $initializer();
                }

                if (is_callable($initializer)) {
                    $this->initializers[] = $initializer;
                    continue;
                }

                throw new InvalidArgumentException(sprintf(
                    'An invalid initializer was registered. A callable or an instance of "%s" was expected, but
                    "%s" was received',
                    InitializerInterface::class,
                    is_object($initializer) ? get_class($initializer) : gettype($initializer)
                ));
            }
        }
    }
}