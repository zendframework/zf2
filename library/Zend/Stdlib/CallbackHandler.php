<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use ReflectionClass;

/**
 * CallbackHandler
 *
 * A handler for an event, event, filterchain, etc. Abstracts PHP callbacks,
 * primarily to allow for lazy-loading and ensuring availability of default
 * arguments (currying).
 */
class CallbackHandler
{
    /**
     * @var string|array|callable PHP callback to invoke
     */
    protected $callback;

    /**
     * Callback metadata, if any
     * @var array
     */
    protected $metadata;

    /**
     * PHP version is greater as 5.4rc1?
     * @var bool
     *
     * @deprecated since 2.4
     */
    protected static $isPhp54 = true;

    /**
     * Constructor
     *
     * @param  string|array|object|callable $callback PHP callback
     * @param  array                        $metadata  Callback metadata
     */
    public function __construct($callback, array $metadata = array())
    {
        $this->metadata  = $metadata;
        $this->registerCallback($callback);
    }

    /**
     * Registers the callback provided in the constructor
     *
     * @param  callable $callback
     * @throws Exception\InvalidCallbackException
     * @return void
     */
    protected function registerCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('Invalid callback provided; not callable');
        }

        $this->callback = $callback;
    }

    /**
     * Retrieve registered callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Invoke handler
     *
     * @param  array $args Arguments to pass to callback
     * @return mixed
     */
    public function call(array $args = array())
    {
        $callback = $this->getCallback();

        $argCount = count($args);

        if (is_string($callback)) {
            $result = $this->validateStringCallbackFor54($callback);

            if ($result !== true && $argCount <= 3) {
                $callback       = $result;
                // Minor performance tweak, if the callback gets called more
                // than once
                $this->callback = $result;
            }
        }

        // Minor performance tweak; use call_user_func_array() with > 3 arguments
        // reached
        switch ($argCount) {
            case 0:
                return $callback();
            case 1:
                return $callback($args[0]);
            case 2:
                return $callback($args[0], $args[1]);
            case 3:
                return $callback($args[0], $args[1], $args[2]);
            default:
                return call_user_func_array($callback, $args);
        }
    }

    /**
     * Invoke as functor
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->call(func_get_args());
    }

    /**
     * Get all callback metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Retrieve a single metadatum
     *
     * @param  string $name
     * @return mixed
     */
    public function getMetadatum($name)
    {
        if (array_key_exists($name, $this->metadata)) {
            return $this->metadata[$name];
        }
        return;
    }

    /**
     * Validate a static method call
     *
     * @param  string $callback
     * @return true|array
     * @throws Exception\InvalidCallbackException if invalid
     */
    protected function validateStringCallbackFor54($callback)
    {
        if (!strstr($callback, '::')) {
            return true;
        }

        list($class, $method) = explode('::', $callback, 2);

        if (!class_exists($class)) {
            throw new Exception\InvalidCallbackException(sprintf(
                'Static method call "%s" refers to a class that does not exist',
                $callback
            ));
        }

        $r = new ReflectionClass($class);
        if (!$r->hasMethod($method)) {
            throw new Exception\InvalidCallbackException(sprintf(
                'Static method call "%s" refers to a method that does not exist',
                $callback
            ));
        }
        $m = $r->getMethod($method);
        if (!$m->isStatic()) {
            throw new Exception\InvalidCallbackException(sprintf(
                'Static method call "%s" refers to a method that is not static',
                $callback
            ));
        }

        // returning a non boolean value may not be nice for a validate method,
        // but that allows the usage of a static string callback without using
        // the call_user_func function.
        return array($class, $method);
    }
}
