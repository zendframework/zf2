<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use Closure;
use ReflectionClass;

/**
 * CallbackHandler
 *
 * A handler for a event, event, filterchain, etc. Abstracts PHP callbacks,
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
     * Constructor
     *
     * @param  Callable $callback PHP callback
     * @param  array    $metadata Callback metadata
     */
    public function __construct(Callable $callback, array $metadata = array())
    {
        $this->metadata = $metadata;

        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback, 2);
        }

        $this->callback = $callback;
    }

    /**
     * Retrieve registered callback
     *
     * @return Callable
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

        // Minor performance tweak; use call_user_func() until > 3 arguments
        // reached
        switch ($argCount) {
            case 0:
                return $callback();
            case 1:
                return $callback(array_shift($args));
            case 2:
                $arg1 = array_shift($args);
                $arg2 = array_shift($args);

                return $callback($arg1, $arg2);
            case 3:
                $arg1 = array_shift($args);
                $arg2 = array_shift($args);
                $arg3 = array_shift($args);

                return $callback($arg1, $arg2, $arg3);
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
        return isset($this->metadata[$name]) ? $this->metadata[$name] : null;
    }
}
