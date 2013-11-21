<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\Zf2Compat\AliasResolverAbstractFactory;
use Zend\ServiceManager\Zf2Compat\PeeringServiceLocatorAbstractFactory;
use Zend\ServiceManager\Zf2Compat\ServiceNameNormalizerAbstractFactory;

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
     * @var string|callable|\Closure|FactoryInterface[]
     */
    protected $factories = array();

    /**
     * @var AbstractFactoryInterface[]
     */
    protected $abstractFactories = array();

    /**
     * @var array[]
     */
    protected $delegators = array();

    /**
     * @var array
     */
    protected $pendingAbstractFactoryRequests = array();

    /**
     * @var integer
     */
    protected $nestedContextCounter = -1;

    /**
     * @var array
     */
    protected $nestedContext = array();

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
    protected $initializers = array();

    /**
     * @var AliasResolverAbstractFactory|null
     */
    private $aliasResolver;

    /**
     * Whether or not to share by default
     *
     * @var bool
     */
    protected $shareByDefault = true;

    /**
     * @var bool Track whether not to throw exceptions during create()
     */
    protected $throwExceptionInCreate = true;

    /**
     * @var ServiceNameNormalizerAbstractFactory|null
     */
    private $canonicalizer;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    /**
     * Set allow override
     *
     * @param $allowOverride
     * @return ServiceManager
     */
    public function setAllowOverride($allowOverride)
    {
        $this->allowOverride = (bool) $allowOverride;

        return $this;
    }

    /**
     * Get allow override
     *
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
                get_class($this) . '::' . __FUNCTION__
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
     * Set throw exceptions in create
     *
     * @param  bool $throwExceptionInCreate
     * @return ServiceManager
     */
    public function setThrowExceptionInCreate($throwExceptionInCreate)
    {
        $this->throwExceptionInCreate = $throwExceptionInCreate;

        return $this;
    }

    /**
     * Get throw exceptions in create
     *
     * @return bool
     */
    public function getThrowExceptionInCreate()
    {
        return $this->throwExceptionInCreate;
    }

    /**
     * Enables retrieval of services through canonical names
     */
    public function useCanonicalNames()
    {
        $this->getCanonicalizer();
    }

    /**
     * Set invokable class
     *
     * @param  string  $name
     * @param  string  $invokableClass
     * @param  bool $shared
     * @return ServiceManager
     * @throws Exception\InvalidServiceNameException
     */
    public function setInvokableClass($name, $invokableClass, $shared = null)
    {
        if ($this->has($name, false)) {
            if ($this->allowOverride === false) {
                throw new Exception\InvalidServiceNameException(sprintf(
                    'A service by the name or alias "%s" already exists and cannot be overridden; please use an alternate name',
                    $name
                ));
            }
            $this->unregisterService($name);
        }

        if ($shared === null) {
            $shared = $this->shareByDefault;
        }

        $this->invokableClasses[$name] = $invokableClass;
        $this->shared[$name]           = (bool) $shared;

        return $this;
    }

    /**
     * Set factory
     *
     * @param  string                           $name
     * @param  string|FactoryInterface|callable $factory
     * @param  bool                             $shared
     * @return ServiceManager
     * @throws Exception\InvalidArgumentException
     * @throws Exception\InvalidServiceNameException
     */
    public function setFactory($name, $factory, $shared = null)
    {
        if (!($factory instanceof FactoryInterface || is_string($factory) || is_callable($factory))) {
            throw new Exception\InvalidArgumentException(
                'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
            );
        }

        if ($this->has($name, false)) {
            if ($this->allowOverride === false) {
                throw new Exception\InvalidServiceNameException(sprintf(
                    'A service by the name or alias "%s" already exists and cannot be overridden, please use an alternate name',
                    $name
                ));
            }

            $this->unregisterService($name);
        }

        if ($shared === null) {
            $shared = $this->shareByDefault;
        }

        $this->factories[$name] = $factory;
        $this->shared[$name]    = (bool) $shared;

        return $this;
    }

    /**
     * Add abstract factory
     *
     * @param  AbstractFactoryInterface|string $factory
     * @param  bool                            $topOfStack
     * @return ServiceManager
     * @throws Exception\InvalidArgumentException if the abstract factory is invalid
     */
    public function addAbstractFactory($factory, $topOfStack = true)
    {
        if (!$factory instanceof AbstractFactoryInterface && is_string($factory)) {
            $factory = new $factory();
        }

        if (!$factory instanceof AbstractFactoryInterface) {
            throw new Exception\InvalidArgumentException(
                'Provided abstract factory must be the class name of an abstract'
                . ' factory or an instance of an AbstractFactoryInterface.'
            );
        }

        if ($topOfStack) {
            array_unshift($this->abstractFactories, $factory);
        } else {
            array_push($this->abstractFactories, $factory);
        }

        return $this;
    }

    /**
     * Sets the given service name as to be handled by a delegator factory
     *
     * @param  string $serviceName          name of the service being the delegate
     * @param  string $delegatorFactoryName name of the service being the delegator factory
     *
     * @return ServiceManager
     */
    public function addDelegator($serviceName, $delegatorFactoryName)
    {
        if (!isset($this->delegators[$serviceName])) {
            $this->delegators[$serviceName] = array();
        }

        $this->delegators[$serviceName][] = $delegatorFactoryName;

        return $this;
    }

    /**
     * Add initializer
     *
     * @param  callable|InitializerInterface $initializer
     * @param  bool                          $topOfStack
     * @return ServiceManager
     * @throws Exception\InvalidArgumentException
     */
    public function addInitializer($initializer, $topOfStack = true)
    {
        if (!($initializer instanceof InitializerInterface || is_callable($initializer))) {
            if (is_string($initializer)) {
                $initializer = new $initializer();
            }

            if (!($initializer instanceof InitializerInterface || is_callable($initializer))) {
                throw new Exception\InvalidArgumentException('$initializer should be callable.');
            }
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
     * @param  string  $name
     * @param  mixed   $service
     * @return ServiceManager
     * @throws Exception\InvalidServiceNameException
     */
    public function setService($name, $service)
    {
        if ($this->has($name, false)) {
            if ($this->allowOverride === false) {
                throw new Exception\InvalidServiceNameException(sprintf(
                    '%s: A service by the name "%s" or alias already exists and cannot be overridden, please use an alternate name.',
                    get_class($this) . '::' . __FUNCTION__,
                    $name
                ));
            }

            $this->unregisterService($name);
        }

        $this->instances[$name] = $service;

        return $this;
    }

    /**
     * @param  string $name
     * @param  bool   $isShared
     * @return ServiceManager
     * @throws Exception\ServiceNotFoundException
     */
    public function setShared($name, $isShared)
    {
        if (
            !isset($this->invokableClasses[$name])
            && !isset($this->factories[$name])
            && !$this->canCreateFromAbstractFactory($name, $name)
        ) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: A service by the name "%s" was not found and could not be marked as shared',
                get_class($this) . '::' . __FUNCTION__,
                $name
            ));
        }

        $this->shared[$name] = (bool) $isShared;

        return $this;
    }

    /**
     * Retrieve a registered instance
     *
     * @param  string  $name
     * @throws Exception\ServiceNotFoundException
     * @return object|array
     */
    public function get($name)
    {
        $isAlias = false;

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $instance = null;

        $this->checkNestedContextStart($name);

        if (
            isset($this->invokableClasses[$name])
            || isset($this->factories[$name])
            || $this->canCreateFromAbstractFactory($name, $name)
        ) {
            $instance = $this->create($name);
        }

        $this->checkNestedContextStop();

        // Still no instance? raise an exception
        if ($instance === null) {
            $this->checkNestedContextStop(true);

            if ($isAlias) {
                throw new Exception\ServiceNotFoundException(sprintf(
                    'An alias "%s" was requested but no service could be found.',
                    $name
                ));
            }

            throw new Exception\ServiceNotFoundException(sprintf(
                '%s was unable to fetch or create an instance for %s',
                get_class($this) . '::' . __FUNCTION__,
                $name
            ));
        }

        if (
            ($this->shareByDefault && !isset($this->shared[$name]))
            || (isset($this->shared[$name]) && $this->shared[$name] === true)
        ) {
            $this->instances[$name] = $instance;
        }

        return $instance;
    }

    /**
     * Create an instance of the requested service
     *
     * @param  string $name
     *
     * @return bool|object
     */
    public function create($name)
    {
        if (isset($this->delegators[$name])) {
            return $this->createDelegatorFromFactory($name);
        }

        return $this->doCreate($name);
    }

    /**
     * Creates a callback that uses a delegator to create a service
     *
     * @param DelegatorFactoryInterface|callable $delegatorFactory the delegator factory
     * @param string                             $name             requested service name
     * @param callable                           $creationCallback callback for instantiating the real service
     *
     * @return callable
     */
    private function createDelegatorCallback($delegatorFactory, $name, callable $creationCallback)
    {
        $serviceManager  = $this;

        return function () use ($serviceManager, $delegatorFactory, $name, $creationCallback) {
            return $delegatorFactory instanceof DelegatorFactoryInterface
                ? $delegatorFactory->createDelegatorWithName($serviceManager, $name, $creationCallback)
                : $delegatorFactory($serviceManager, $name, $name, $creationCallback);
        };
    }

    /**
     * Actually creates the service
     *
     * @param string $name real service name
     *
     * @return bool|mixed|null|object
     * @throws Exception\ServiceNotFoundException
     *
     * @internal this method is internal because of PHP 5.3 compatibility - do not explicitly use it
     */
    public function doCreate($name)
    {
        $instance = null;

        if (isset($this->factories[$name])) {
            $instance = $this->createFromFactory($name);
        }

        if ($instance === null && isset($this->invokableClasses[$name])) {
            $instance = $this->createFromInvokable($name);
        }
        $this->checkNestedContextStart($name);
        if ($instance === null && $this->canCreateFromAbstractFactory($name)) {
            $instance = $this->createFromAbstractFactory($name);
        }
        $this->checkNestedContextStop();

        if ($instance === null && $this->throwExceptionInCreate) {
            $this->checkNestedContextStop(true);

            throw new Exception\ServiceNotFoundException(sprintf(
                'No valid instance was found for %s',
                $name
            ));
        }

        // Do not call initializers if we do not have an instance
        if ($instance === null) {
            return $instance;
        }

        foreach ($this->initializers as $initializer) {
            if ($initializer instanceof InitializerInterface) {
                $initializer->initialize($instance, $this);
            } else {
                call_user_func($initializer, $instance, $this);
            }
        }

        return $instance;
    }

    /**
     * Determine if we can create an instance.
     * Proxies to has()
     *
     * @param  string|array $name
     * @param  bool         $checkAbstractFactories
     * @return bool
     * @deprecated this method is being deprecated as of zendframework 2.3, and may be removed in future major versions
     */
    public function canCreate($name, $checkAbstractFactories = true)
    {
        trigger_error(sprintf('%s is deprecated; please use %s::has', __METHOD__, __CLASS__), E_USER_DEPRECATED);
        return $this->has($name, $checkAbstractFactories, false);
    }

    /**
     * Determine if an instance exists.
     *
     * @param  string|array  $name  An array argument accepts exactly two values.
     *                              Example: array('canonicalName', 'requestName')
     * @param  bool          $checkAbstractFactories
     * @return bool
     */
    public function has($name, $checkAbstractFactories = true)
    {
        if (is_array($name)) {
            list($name) = $name;
        }

        return (
            isset($this->invokableClasses[$name])
            || isset($this->factories[$name])
            || isset($this->instances[$name])
            || ($checkAbstractFactories && $this->canCreateFromAbstractFactory($name, $name))
        );
    }

    /**
     * Determine if we can create an instance from an abstract factory.
     *
     * @param  string $name
     * @return bool
     */
    public function canCreateFromAbstractFactory($name)
    {
        if (array_key_exists($name, $this->nestedContext)) {
            $context = $this->nestedContext[$name];

            if ($context === false) {
                return false;
            } elseif (is_object($context)) {
                return !isset($this->pendingAbstractFactoryRequests[get_class($context) . $name]);
            }
        }

        $this->checkNestedContextStart($name);
        // check abstract factories
        $result = false;
        $this->nestedContext[$name] = false;

        foreach ($this->abstractFactories as $abstractFactory) {
            $pendingKey = get_class($abstractFactory) . $name;
            if (isset($this->pendingAbstractFactoryRequests[$pendingKey])) {
                $result = false;
                break;
            }

            if ($abstractFactory->canCreateServiceWithName($this, $name)) {
                $this->nestedContext[$name] = $abstractFactory;
                $result = true;

                break;
            }
        }

        $this->checkNestedContextStop();

        return $result;
    }

    /**
     * @param  string $alias
     * @param  string $nameOrAlias
     *
     * @return ServiceManager
     *
     * @throws Exception\ServiceNotFoundException
     * @throws Exception\InvalidServiceNameException
     *
     * @deprecated please don't use aliases, or Ocramius will curse you
     * @deprecated seriously, don't
     * @deprecated I said don't.
     */
    public function setAlias($alias, $nameOrAlias)
    {
        if ($this->has($alias, false)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'Service "%s" is already defined, and an alias for it cannot be set',
                $alias
            ));
        }

        $this->getAliasResolver()->setAlias($alias, $nameOrAlias);

        return $this;
    }

    /**
     * Determine if we have an alias
     *
     * @param  string $alias
     * @return bool
     */
    public function hasAlias($alias)
    {
        return $this->getAliasResolver()->hasAlias($alias);
    }

    /**
     * Create scoped service manager
     *
     * @param  string $peering
     * @return ServiceManager
     */
    public function createScopedServiceManager($peering = self::SCOPE_PARENT)
    {
        $manager = new ServiceManager();

        if ($peering == self::SCOPE_PARENT) {
            $manager->addAbstractFactory(new PeeringServiceLocatorAbstractFactory($this));
        }

        if ($peering == self::SCOPE_CHILD) {
            $this->addAbstractFactory(new PeeringServiceLocatorAbstractFactory($manager));
        }

        return $manager;
    }

    /**
     * Add a peering relationship
     *
     * @param  ServiceManager $manager
     * @param  string         $peering
     * @return ServiceManager
     */
    public function addPeeringServiceManager(ServiceManager $manager, $peering = self::SCOPE_PARENT)
    {
        if ($peering == self::SCOPE_PARENT) {
            $this->addAbstractFactory(new PeeringServiceLocatorAbstractFactory($manager));
        }

        if ($peering == self::SCOPE_CHILD) {
            $manager->addAbstractFactory(new PeeringServiceLocatorAbstractFactory($this));
        }

        return $this;
    }

    /**
     * Create service via callback
     *
     * @param  callable $callable
     * @param  string   $name
     * @throws Exception\ServiceNotCreatedException
     * @throws Exception\ServiceNotFoundException
     * @throws Exception\CircularDependencyFoundException
     * @return object
     */
    protected function createServiceViaCallback($callable, $name)
    {
        static $circularDependencyResolver = array();
        $depKey = spl_object_hash($this) . '-' . $name;

        if (isset($circularDependencyResolver[$depKey])) {
            $circularDependencyResolver = array();
            throw new Exception\CircularDependencyFoundException(
                'Circular dependency for LazyServiceLoader was found for instance ' . $name
            );
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
            'aliases'   => $this->aliasResolver ? array_keys($this->aliasResolver->getAliases()) : [],
            'instances' => array_keys($this->instances),
        );
    }

    /**
     * Attempt to create an instance via an invokable class
     *
     * @param  string $name
     * @return null|\stdClass
     * @throws Exception\ServiceNotFoundException If resolved class does not exist
     */
    protected function createFromInvokable($name)
    {
        $invokable = $this->invokableClasses[$name];

        if (!class_exists($invokable)) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: failed retrieving "%s" via invokable class "%s"; class does not exist',
                get_class($this) . '::' . __FUNCTION__,
                $name,
                $invokable
            ));
        }

        return $instance = new $invokable;
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
            return $this->createServiceViaCallback(array($factory, 'createService'), $name);
        }

        if (is_callable($factory)) {
            return $this->createServiceViaCallback($factory, $name);
        }

        throw new Exception\ServiceNotCreatedException(sprintf(
            'While attempting to create %s an invalid factory was registered for this instance type.',
            $name
        ));
    }

    /**
     * Attempt to create an instance via an abstract factory
     *
     * @param  string $name
     * @return object|null
     * @throws Exception\ServiceNotCreatedException If abstract factory is not callable
     */
    protected function createFromAbstractFactory($name)
    {
        if (isset($this->nestedContext[$name])) {
            $abstractFactory = $this->nestedContext[$name];
            $pendingKey = get_class($abstractFactory) . $name;
            try {
                $this->pendingAbstractFactoryRequests[$pendingKey] = true;

                $instance = $this->createServiceViaCallback(
                    array($abstractFactory, 'createServiceWithName'),
                    $name
                );
                unset($this->pendingAbstractFactoryRequests[$pendingKey]);

                return $instance;
            } catch (\Exception $e) {
                unset($this->pendingAbstractFactoryRequests[$pendingKey]);
                $this->checkNestedContextStop(true);

                throw new Exception\ServiceNotCreatedException(
                    sprintf(
                        'An abstract factory could not create an instance of %s.',
                        $name
                    ),
                    $e->getCode(),
                    $e
                );
            }
        }

        return null;
    }

    /**
     *
     * @param string $cName
     * @return self
     */
    protected function checkNestedContextStart($cName)
    {
        if ($this->nestedContextCounter === -1 || !isset($this->nestedContext[$cName])) {
            $this->nestedContext[$cName] = null;
        }
        $this->nestedContextCounter++;
        return $this;
    }

    /**
     *
     * @param bool $force
     * @return self
     */
    protected function checkNestedContextStop($force = false)
    {
        if ($force) {
            $this->nestedContextCounter = -1;
            $this->nestedContext = array();
            return $this;
        }

        $this->nestedContextCounter--;
        if ($this->nestedContextCounter === -1) {
            $this->nestedContext = array();
        }
        return $this;
    }

    /**
     * @param  string $name
     * @return mixed
     * @throws Exception\ServiceNotCreatedException
     */
    protected function createDelegatorFromFactory($name)
    {
        $serviceManager     = $this;
        $delegatorsCount    = count($this->delegators[$name]);
        $creationCallback   = function () use ($serviceManager, $name) {
            return $serviceManager->doCreate($name);
        };

        for ($i = 0; $i < $delegatorsCount; $i += 1) {

            $delegatorFactory = $this->delegators[$name][$i];

            if (is_string($delegatorFactory)) {
                $delegatorFactory = !$this->has($delegatorFactory) && class_exists($delegatorFactory, true) ?
                    new $delegatorFactory
                    : $this->get($delegatorFactory);
                $this->delegators[$name][$i] = $delegatorFactory;
            }

            if (!$delegatorFactory instanceof DelegatorFactoryInterface && !is_callable($delegatorFactory)) {
                throw new Exception\ServiceNotCreatedException(sprintf(
                    'While attempting to create %s an invalid factory was registered for this instance type.',
                    $name
                ));
            }

            $creationCallback = $this->createDelegatorCallback(
                $delegatorFactory,
                $name,
                $creationCallback
            );
        }

        return $creationCallback($serviceManager, $name, $creationCallback);
    }

    /**
     * Unregister a service
     *
     * Called when $allowOverride is true and we detect that a service being
     * added to the instance already exists. This will remove the duplicate
     * entry, and also any shared flags previously registered.
     *
     * @param  string $canonical
     * @return void
     */
    protected function unregisterService($canonical)
    {
        $types = array('invokableClasses', 'factories');
        foreach ($types as $type) {
            if (isset($this->{$type}[$canonical])) {
                unset($this->{$type}[$canonical]);
                break;
            }
        }

        if ($this->aliasResolver) {
            $this->getAliasResolver()->removeAlias($canonical);
        }

        if (isset($this->instances[$canonical])) {
            unset($this->instances[$canonical]);
        }

        if (isset($this->shared[$canonical])) {
            unset($this->shared[$canonical]);
        }
    }

    /**
     * @return AliasResolverAbstractFactory
     */
    private function getAliasResolver()
    {
        if (isset($this->aliasResolver)) {
            return $this->aliasResolver;
        }

        $this->aliasResolver = new AliasResolverAbstractFactory($this);

        $this->addAbstractFactory($this->aliasResolver, false);

        return $this->aliasResolver;
    }

    /**
     * @return ServiceNameNormalizerAbstractFactory
     */
    private function getCanonicalizer()
    {
        if (isset($this->canonicalizer)) {
            return $this->canonicalizer;
        }

        $this->canonicalizer = new ServiceNameNormalizerAbstractFactory($this);

        $this->addAbstractFactory($this->canonicalizer, false);

        return $this->canonicalizer;
    }
}
