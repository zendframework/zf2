<?php

namespace Zend\Di\ServiceLocator;

use Zend\Di\Di;
use Zend\Di\Exception;

class DependencyInjectorProxy extends Di
{
    /**
     * @var Di
     */
    protected $di;

    /**
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->di              = $di;
        $this->definitions     = $di->definitions();
        $this->instanceManager = $di->instanceManager();
    }

    /**
     * Override, as we want it to use the functionality defined in the proxy
     *
     * @param  string $name
     * @param  array $params
     * @return GeneratorInstance
     */
    public function get($name, array $params = array())
    {
        return parent::get($name, $params);
    }
    /**
     * {@inheritDoc}
     */
    public function newInstance($name, array $params = array(), $isShared = true)
    {
        $instance = parent::newInstance($name, $params, $isShared);

        if ($instance instanceof GeneratorInstance) {
            /* @var $instance GeneratorInstance */
            $instance->setShared($isShared);
        }

        return $instance;
    }

    /**
     * Override createInstanceViaConstructor method from injector
     *
     * Returns code generation artifacts.
     *
     * @param  string $class
     * @param  null|array $params
     * @param  null|string $alias
     * @return GeneratorInstance
     */
    public function createInstanceViaConstructor($class, $params, $alias = null)
    {
        $callParameters = array();

        if ($this->di->definitions->hasMethod($class, '__construct')
            && (count($this->di->definitions->getMethodParameters($class, '__construct')) > 0)
        ) {
            $callParameters = $this->resolveMethodParameters($class, '__construct', $params, $alias, true, true);
            $callParameters = $callParameters ?: array();
        }

        return new GeneratorInstance($class, $alias, '__construct', $callParameters);
    }

    /**
     * Override instance creation via callback
     *
     * @param  callback $callback
     * @param  null|array $params
     * @return GeneratorInstance
     * @throws Exception\InvalidCallbackException
     */
    public function createInstanceViaCallback($callback, $params, $alias)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('An invalid constructor callback was provided');
        }

        // @todo add support for string callbacks?

        if (!is_array($callback) || is_object($callback[0])) {
            throw new Exception\InvalidCallbackException('For purposes of service locator generation, constructor callbacks must refer to static methods only');
        }

        $class  = $callback[0];
        $method = $callback[1];

        $callParameters = array();
        if ($this->di->definitions->hasMethod($class, $method)) {
            $callParameters = $this->resolveMethodParameters($class, $method, $params, $alias, true, true);
        }

        $callParameters = $callParameters ?: array();

        return new GeneratorInstance(null, $alias, $callback, $callParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function handleInjectionMethodForObject($class, $method, $params, $alias, $isRequired)
    {
        return array(
            'method' => $method,
            'params' =>  $this->resolveMethodParameters($class, $method, $params, $alias, $isRequired),
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveAndCallInjectionMethodForInstance($instance, $method, $params, $alias, $methodIsRequired, $methodClass = null)
    {
        if (!$instance instanceof GeneratorInstance) {
            return parent::resolveAndCallInjectionMethodForInstance($instance, $method, $params, $alias, $methodIsRequired, $methodClass);
        }

        /* @var $instance GeneratorInstance */
        $methodClass = $instance->getClass();
        $callParameters = $this->resolveMethodParameters($methodClass, $method, $params, $alias, $methodIsRequired);

        if ($callParameters !== false) {
            $instance->addMethod(array(
                'method' => $method,
                'params' => $callParameters,
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getClass($instance)
    {
        return $instance instanceof GeneratorInstance ? $instance->getClass() : get_class($instance);
    }
}
