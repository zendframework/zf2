<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\ServiceManager;

class ServiceManager implements ServiceLocatorInterface
{

    /**@#+
     * Constants
     */
    const SCOPE_PARENT = 'parent';
    const SCOPE_CHILD = 'child';
    /**@#-*/

    /**
     * @var bool
     */
    protected $allowOverride = false;

    /**
     * @var array
     */
    protected $invokableClasses = array();

    /**
     * @var string|callable|Closure|InstanceFactoryInterface[]
     */
    protected $factories = array();

    /**
     * @var AbstractFactoryInterface[]
     */
    protected $abstractFactories = array();

    /**
     * @var array
     */
    protected $pendingAbstractFactoryRequests = array();

    /**
     * @var array
     */
    protected $shared = array();

    /**
     * Registered services and cached values
     *
     * @var array
     */
    protected $instances = array();

    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * @var array
     */
    protected $initializers = array();

    /**
     * @var ServiceManager[]
     */
    protected $peeringServiceManagers = array();

    /**
     * Whether or not to share by default
     *
     * @var bool
     */
    protected $shareByDefault = true;

    /**
     * @var bool
     */
    protected $retrieveFromPeeringManagerFirst = false;

    /**
     * @var bool Track whether not ot throw exceptions during create()
     */
    protected $throwExceptionInCreate = true;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        if ($configuration) {
            $configuration->configureServiceManager($this);
        }
    }

    /**
     * @param $allowOverride
     */
    public function setAllowOverride($allowOverride)
    {
        $this->allowOverride = (bool) $allowOverride;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowOverride()
    {
        return $this->allowOverride;
    }

    /**
     * Set flag indicating whether services are shared by default
     *
     * @param  bool $shareByDefault
     * @return ServiceManager
     * @throws Exception\RuntimeException if allowOverride is false
     */
    public function setShareByDefault($shareByDefault)
    {
        if ($this->allowOverride === false) {
            throw new Exception\RuntimeException(sprintf(
                '%s: cannot alter default shared service setting; container is marked immutable (allow_override is false)',
                __METHOD__
            ));
        }
        $this->shareByDefault = (bool) $shareByDefault;
        return $this;
    }

    /**
     * Are services shared by default?
     *
     * @return bool
     */
    public function shareByDefault()
    {
        return $this->shareByDefault;
    }

    /**
     * @param bool $throwExceptionInCreate
     * @return ServiceManager
     */
    public function setThrowExceptionInCreate($throwExceptionInCreate)
    {
        $this->throwExceptionInCreate = $throwExceptionInCreate;

        return $this;
    }

    /**
     * @return bool
     */
    public function getThrowExceptionInCreate()
    {
        return $this->throwExceptionInCreate;
    }

    /**
     * Set flag indicating whether to pull from peering manager before attempting creation
     *
     * @param  bool $retrieveFromPeeringManagerFirst
     * @return ServiceManager
     */
    public function setRetrieveFromPeeringManagerFirst($retrieveFromPeeringManagerFirst = true)
    {
        $this->retrieveFromPeeringManagerFirst = (bool) $retrieveFromPeeringManagerFirst;

        return $this;
    }

    /**
     * Should we retrieve from the peering manager prior to attempting to create a service?
     *
     * @return bool
     */
    public function retrieveFromPeeringManagerFirst()
    {
        return $this->retrieveFromPeeringManagerFirst;
    }

    /**
     * @param $name
     * @param $invokableClass
     * @param bool $shared
     * @throws Exception\InvalidServiceNameException
     */
    public function setInvokableClass($name, $invokableClass, $shared = true)
    {
        if ($this->allowOverride === false && $this->has($name, false)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'A service by the name or alias "%s" already exists and cannot be overridden; please use an alternate name',
                $name
            ));
        }

        $this->invokableClasses[$name] = $invokableClass;
        $this->shared[$name] = $shared;
        return $this;
    }

    /**
     * @param $name
     * @param $factory
     * @throws Exception\InvalidServiceNameException
     */
    public function setFactory($name, $factory, $shared = true)
    {
        if (!is_string($factory) && !$factory instanceof FactoryInterface && !is_callable($factory)) {
            throw new Exception\InvalidArgumentException(
                'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
            );
        }

        if ($this->allowOverride === false && $this->has($name, false)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'A service by the name or alias "%s" already exists and cannot be overridden, please use an alternate name',
                $name
            ));
        }

        $this->factories[$name] = $factory;
        $this->shared[$name] = $shared;
        return $this;
    }

    /**
     * @param AbstractFactoryInterface|string $factory
     * @param bool $topOfStack
     * @throws Exception\InvalidArgumentException if the abstract factory is invalid
     */
    public function addAbstractFactory($factory, $topOfStack = true)
    {
        if (!is_string($factory) && !$factory instanceof AbstractFactoryInterface) {
            throw new Exception\InvalidArgumentException(
                'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
            );
        }
        if (is_string($factory)) {
            if (!class_exists($factory, true)) {
                throw new Exception\InvalidArgumentException(
                    'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
                );
            }
            $refl = new \ReflectionClass($factory);
            if (!$refl->implementsInterface(__NAMESPACE__ . '\\AbstractFactoryInterface')) {
                throw new Exception\InvalidArgumentException(
                    'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
                );
            }
        }

        if ($topOfStack) {
            array_unshift($this->abstractFactories, $factory);
        } else {
            array_push($this->abstractFactories, $factory);
        }
        return $this;
    }

    /**
     * @param $initializer
     * @throws Exception\InvalidArgumentException
     */
    public function addInitializer($initializer, $topOfStack = true)
    {
        if (!is_callable($initializer) && !$initializer instanceof InitializerInterface) {
            throw new Exception\InvalidArgumentException('$initializer should be callable.');
        }

        if ($topOfStack) {
            array_unshift($this->initializers, $initializer);
        } else {
            array_push($this->initializers, $initializer);
        }
        return $this;
    }

    /**
     * Register a service with the locator
     *
     * @param string $name
     * @param mixed $service
     * @param bool $shared
     * @param bool $shared
     * @return ServiceManager
     * @throws Exception\InvalidServiceNameException
     */
    public function setService($name, $service, $shared = true)
    {
        if ($this->allowOverride === false && $this->has($name, false)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                '%s: A service by the name "%s" or alias already exists and cannot be overridden, please use an alternate name.',
                __METHOD__,
                $name
            ));
        }

        /**
         * @todo If a service is being overwritten, destroy all previous aliases
         */

        $this->instances[$name] = $service;
        $this->shared[$name] = (bool) $shared;
        return $this;
    }

    /**
     * @param $name
     * @param $isShared
     * @return ServiceManager
     * @throws Exception\ServiceNotFoundException
     */
    public function setShared($name, $isShared)
    {
        if (!isset($this->invokableClasses[$name]) && !isset($this->factories[$name])) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: A service by the name "%s" was not found and could not be marked as shared',
                __METHOD__,
                $name
            ));
        }

        $this->shared[$name] = (bool) $isShared;
        return $this;
    }

    /**
     * Retrieve a registered instance
     *
     * @param  string $cName
     * @param  array $params
     * @return mixed
     */
    public function get($name, $usePeeringServiceManagers = true)
    {
        if ($this->hasAlias($name)) {
            do {
                $name = $this->aliases[$name];
            } while ($this->hasAlias($name));

            if (!$this->has($name)) {
                throw new Exception\ServiceNotFoundException(sprintf(
                    'An alias "%s" was requested but no service could be found.',
                    $name
                ));
            }
        }

        $instance = null;

        if (isset($this->instances[$name])) {
            $instance = $this->instances[$name];
        }

        $selfException = null;

        if (!$instance && !is_array($instance)) {
            $retrieveFromPeeringManagerFirst = $this->retrieveFromPeeringManagerFirst();
            if ($usePeeringServiceManagers && $retrieveFromPeeringManagerFirst) {
                $instance = $this->retrieveFromPeeringManager($name);
            }
            if (!$instance) {
                if ($this->canCreate($name)) {
                    $instance = $this->create($name);
                } elseif ($usePeeringServiceManagers && !$retrieveFromPeeringManagerFirst) {
                    $instance = $this->retrieveFromPeeringManager($name);
                }
            }
        }

        // Still no instance? raise an exception
        if (!$instance && !is_array($instance)) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s was unable to fetch or create an instance for %s',
                    __METHOD__,
                    $name
                ),
                null,
                ($selfException === null) ? null : $selfException->getPrevious()
            );
        }

        if ($this->shareByDefault()
            && !isset($this->instances[$name])
            && (!isset($this->shared[$name]) || $this->shared[$name] === true)
        ) {
            $this->instances[$name] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $name
     * @return false|object
     * @throws Exception\ServiceNotCreatedException
     * @throws Exception\InvalidServiceNameException
     */
    public function create($name)
    {
        $instance = false;

        if (isset($this->factories[$name])) {
            $instance = $this->createFromFactory($name);
        }

        if (!$instance && isset($this->invokableClasses[$name])) {
            $instance = $this->createFromInvokable($name);
        }

        if (!$instance && $this->canCreateFromAbstractFactory($name)) {
            $instance = $this->createFromAbstractFactory($name);
        }

        if ($this->throwExceptionInCreate == true && $instance === false) {
            throw new Exception\ServiceNotFoundException(sprintf(
                'No valid instance was found for %s%s',
                $name,
                ($name ? '(alias: ' . $name . ')' : '')
            ));
        }

        foreach ($this->initializers as $initializer) {
            if ($initializer instanceof InitializerInterface) {
                $initializer->initialize($instance, $this);
            } elseif (is_object($initializer) && is_callable($initializer)) {
                $initializer($instance, $this);
            } else {
                call_user_func($initializer, $instance, $this);
            }
        }

        return $instance;
    }

    /**
     * Determine if we can create an instance.
     * @param $name
     * @return bool
     */
    public function canCreate($name, $checkAbstractFactories = true)
    {
        $has = (
            isset($this->invokableClasses[$name])
            || isset($this->factories[$name])
            || isset($this->aliases[$name])
            || isset($this->instances[$name])
        );

        if ($has) {
            return true;
        }

        if (isset($this->factories[$name])) {
            return true;
        }

        if (isset($this->invokableClasses[$name])) {
            return true;
        }

        if ($checkAbstractFactories && $this->canCreateFromAbstractFactory($name)) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = true)
    {
        if ($this->canCreate($name, $checkAbstractFactories)) {
            return true;
        }

        if ($usePeeringServiceManagers) {
            foreach ($this->peeringServiceManagers as $peeringServiceManager) {
                if ($peeringServiceManager->has($name)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if we can create an instance from an abstract factory.
     *
     * @param  string $name
     * @return bool
     */
    public function canCreateFromAbstractFactory($name)
    {
        // check abstract factories
        foreach ($this->abstractFactories as $index => $abstractFactory) {
            // Support string abstract factory class names
            if (is_string($abstractFactory) && class_exists($abstractFactory, true)) {
                $this->abstractFactory[$index] = $abstractFactory = new $abstractFactory();
            }

            if (
                isset($this->pendingAbstractFactoryRequests[get_class($abstractFactory)])
                && $this->pendingAbstractFactoryRequests[get_class($abstractFactory)] == $name
            ) {
                return false;
            }

            if ($abstractFactory->canCreateServiceWithName($this, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $alias
     * @param $nameOrAlias
     * @return ServiceManager
     * @throws Exception\ServiceNotFoundException
     * @throws Exception\InvalidServiceNameException
     */
    public function setAlias($alias, $nameOrAlias)
    {
        if (!is_string($alias) || !is_string($nameOrAlias)) {
            throw new Exception\InvalidServiceNameException('Service or alias names must be strings.');
        }

        if ($alias == '' || $nameOrAlias == '') {
            throw new Exception\InvalidServiceNameException('Invalid service name alias');
        }

        if ($this->allowOverride === false && $this->has($alias, false)) {
            throw new Exception\InvalidServiceNameException('An alias by this name already exists');
        }

        $this->aliases[$alias] = $nameOrAlias;
        return $this;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function hasAlias($alias)
    {
        return (isset($this->aliases[$alias]));
    }

    /**
     * @param string $peering
     * @return ServiceManager
     */
    public function createScopedServiceManager($peering = self::SCOPE_PARENT)
    {
        $scopedServiceManager = new ServiceManager();

        if ($peering == self::SCOPE_PARENT) {
            $scopedServiceManager->peeringServiceManagers[] = $this;
        }

        if ($peering == self::SCOPE_CHILD) {
            $this->peeringServiceManagers[] = $scopedServiceManager;
        }

        return $scopedServiceManager;
    }

    /**
     * Add a peering relationship
     *
     * @param  ServiceManager $manager
     * @param  string $peering
     * @return ServiceManager Current instance
     */
    public function addPeeringServiceManager(ServiceManager $manager, $peering = self::SCOPE_PARENT)
    {
        if ($peering == self::SCOPE_PARENT) {
            $this->peeringServiceManagers[] = $manager;
        }

        if ($peering == self::SCOPE_CHILD) {
            $manager->peeringServiceManagers[] = $this;
        }

        return $this;
    }

    /**
     * @param \callable $callable
     * @param $cName
     * @param $rName
     * @throws Exception\ServiceNotCreatedException
     * @throws Exception\CircularDependencyFoundException
     * @return object
     */
    protected function createServiceViaCallback($callable, $name)
    {
        static $circularDependencyResolver = array();
        $depKey = spl_object_hash($this) . '-' . $name;

        if (isset($circularDependencyResolver[$depKey])) {
            $circularDependencyResolver = array();
            throw new Exception\CircularDependencyFoundException('Circular dependency for LazyServiceLoader was found for instance ' . $name);
        }

        try {
            $circularDependencyResolver[$depKey] = true;
            $instance = call_user_func($callable, $this, $name);
            unset($circularDependencyResolver[$depKey]);
        } catch (Exception\ServiceNotFoundException $e) {
            unset($circularDependencyResolver[$depKey]);
            throw $e;
        } catch (\Exception $e) {
            unset($circularDependencyResolver[$depKey]);
            throw new Exception\ServiceNotCreatedException(
                sprintf('An exception was raised while creating "%s"; no instance returned', $name),
                $e->getCode(),
                $e
            );
        }

        if ($instance === null) {
            throw new Exception\ServiceNotCreatedException('The factory was called but did not return an instance.');
        }

        return $instance;
    }

    /**
     * Retrieve a keyed list of all registered services. Handy for debugging!
     *
     * @return array
     */
    public function getRegisteredServices()
    {
        return array(
            'invokableClasses' => array_keys($this->invokableClasses),
            'factories' => array_keys($this->factories),
            'aliases' => array_keys($this->aliases),
            'instances' => array_keys($this->instances),
        );
    }

    /**
     * Attempt to retrieve an instance via a peering manager
     *
     * @param  string $name
     * @return mixed
     */
    protected function retrieveFromPeeringManager($name)
    {
        foreach ($this->peeringServiceManagers as $peeringServiceManager) {
            if ($peeringServiceManager->has($name)) {
                return $peeringServiceManager->get($name);
            }
        }

        return null;
    }

    /**
     * Attempt to create an instance via an invokable class
     *
     * @param  string $name
     * @return null|\stdClass
     * @throws Exception\ServiceNotCreatedException If resolved class does not exist
     */
    protected function createFromInvokable($name)
    {
        $invokable = $this->invokableClasses[$name];

        if (!class_exists($invokable)) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: failed retrieving "%s%s" via invokable class "%s"; class does not exist',
                __METHOD__,
                $name,
                ($name ? '(alias: ' . $name . ')' : ''),
                $name
            ));
        }

        $instance = new $invokable;
        return $instance;
    }

    /**
     * Attempt to create an instance via a factory
     *
     * @param  string $name
     * @return mixed
     * @throws Exception\ServiceNotCreatedException If factory is not callable
     */
    protected function createFromFactory($name)
    {
        $factory = $this->factories[$name];

        if (is_string($factory) && class_exists($factory, true)) {
            $factory = new $factory;
            $this->factories[$name] = $factory;
        }

        if ($factory instanceof FactoryInterface) {
            $instance = $this->createServiceViaCallback(array($factory, 'createService'), $name);
        } elseif (is_callable($factory)) {
            $instance = $this->createServiceViaCallback($factory, $name);
        } else {
            throw new Exception\ServiceNotCreatedException(sprintf(
                'While attempting to create %s%s an invalid factory was registered for this instance type.',
                $name,
                ($name ? '(alias: ' . $name . ')' : '')
            ));
        }

        return $instance;
    }

    /**
     * Attempt to create an instance via an abstract factory
     *
     * @param  string $name
     * @return \stdClass|null
     * @throws Exception\ServiceNotCreatedException If abstract factory is not callable
     */
    protected function createFromAbstractFactory($name)
    {
        foreach ($this->abstractFactories as $index => $abstractFactory) {
            // support factories as strings
            if (is_string($abstractFactory) && class_exists($abstractFactory, true)) {
                $this->abstractFactories[$index] = $abstractFactory = new $abstractFactory;
            } elseif (!$abstractFactory instanceof AbstractFactoryInterface) {
                throw new Exception\ServiceNotCreatedException(sprintf(
                    'While attempting to create %s%s an abstract factory could not produce a valid instance.',
                    $name,
                    ($name ? '(alias: ' . $name . ')' : '')
                ));
            }

            try {
                $this->pendingAbstractFactoryRequests[get_class($abstractFactory)] = $name;
                $instance = $this->createServiceViaCallback(
                    array($abstractFactory, 'createServiceWithName'),
                    $name,
                    $name
                );
                unset($this->pendingAbstractFactoryRequests[get_class($abstractFactory)]);
            } catch (\Exception $e) {
                unset($this->pendingAbstractFactoryRequests[get_class($abstractFactory)]);
                throw new Exception\ServiceNotCreatedException(
                    sprintf(
                        'An abstract factory could not create an instance of %s%s.',
                        $name,
                        ($name ? '(alias: ' . $name . ')' : '')
                    ),
                    $e->getCode(),
                    $e
                );
            }

            if (is_object($instance)) {
                break;
            }
        }

        return $instance;
    }
}
