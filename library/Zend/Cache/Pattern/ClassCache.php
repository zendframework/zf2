<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Zend\Cache,
    Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClassCache extends CallbackCache
{
    /**
     * The entity
     *
     * @var null|string
     */
    protected $entity = null;

    /**
     * Cache by default
     *
     * @var bool
     */
    protected $cacheByDefault = true;

    /**
     * Cache methods
     *
     * @var array
     */
    protected $cacheMethods = array();

    /**
     * Non-cache methods
     *
     * @var array
     */
    protected $nonCacheMethods = array();

    /**
     * Constructor
     *
     * @param  array|\Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!$this->getEntity()) {
            throw new Exception\InvalidArgumentException("Missing option 'entity'");
        } elseif (!$this->getStorage()) {
            throw new Exception\InvalidArgumentException("Missing option 'storage'");
        }
    }

    /**
     * Get all pattern options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        $options['entity']            = $this->getEntity();
        $options['cache_by_default']  = $this->getCacheByDefault();
        $options['cache_methods']     = $this->getCacheMethods();
        $options['non_cache_methods'] = $this->getNonCacheMethods();
        return $options;
    }

    /**
     * Set the entity to cache
     *
     * @param  string $entity The entity as classname
     * @return ClassCache
     */
    public function setEntity($entity)
    {
        if (!is_string($entity)) {
            throw new Exception\InvalidArgumentException('Invalid entity, must be a classname');
        }
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get the entity to cache
     *
     * @return null|string The classname or NULL if no entity was set
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Enable or disable caching of methods by default.
     *
     * @param  boolean $flag
     * @return ClassCache
     */
    public function setCacheByDefault($flag)
    {
        $this->cacheByDefault = (bool) $flag;
        return $this;
    }

    /**
     * Caching methods by default enabled.
     *
     * return boolean
     */
    public function getCacheByDefault()
    {
        return $this->cacheByDefault;
    }

    /**
     * Enable cache methods
     *
     * @param  string[] $methods
     * @return ClassCache
     */
    public function setCacheMethods(array $methods)
    {
        $this->cacheMethods = array_values(array_unique(array_map(function($method) {
            return strtolower($method);
        }, $methods)));

        return $this;
    }

    /**
     * Get enabled cache methods
     *
     * @return string[]
     */
    public function getCacheMethods()
    {
        return $this->cacheMethods;
    }

    /**
     * Disable cache methods
     *
     * @param  string[] $methods
     * @return ClassCache
     */
    public function setNonCacheMethods(array $methods)
    {
        $this->nonCacheMethods = array_values(array_unique(array_map(function($method) {
            return strtolower($method);
        }, $methods)));

        return $this;
    }

    /**
     * Get disabled cache methods
     *
     * @return string[]
     */
    public function getNonCacheMethods()
    {
        return $this->nonCacheMethods;
    }

    /**
     * Call and cache a class method
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @param  array  $options Cache options
     * @return mixed
     * @throws Exception
     */
    public function call($method, array $args = array(), array $options = array())
    {
        $classname = $this->getEntity();
        $method    = strtolower($method);
        $callback  = $classname . '::' . $method;

        $cache = $this->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $this->getNonCacheMethods());
        } else {
            $cache = in_array($method, $this->getCacheMethods());
        }

        if (!$cache) {
            if ($args) {
                return call_user_func_array($callback, $args);
            } else {
                return $classname::$method();
            }
        }

        // speed up key generation
        if (!isset($options['callback_key'])) {
            $options['callback_key'] = $callback;
        }

        return parent::call($callback, $args, $options);
    }

    /**
     * Generate a key from the method name and arguments
     *
     * @param  string   $method  The method name
     * @param  array    $args    Method arguments
     * @return string
     * @throws Exception
     */
    public function generateKey($method, array $args = array(), array $options = array())
    {
        // speed up key generation
        if (!isset($options['callback_key'])) {
            $callback = $this->getEntity() . '::' . strtolower($method);
            $options['callback_key'] = $callback;
        } else {
            $callback = $this->getEntity() . '::' . $method;
        }

        return parent::generateKey($callback, $args, $options);
    }

    /**
     * Calling a method of the entity.
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, array $args)
    {
        return $this->call($method, $args);
    }

    /**
     * Set a static property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     * @see   http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $class = $this->getEntity();
        $class::$name = $value;
    }

    /**
     * Get a static property
     *
     * @param  string $name
     * @return mixed
     * @see    http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        $class = $this->getEntity();
        return $class::$name;
    }

    /**
     * Is a static property exists.
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $class = $this->getEntity();
        return isset($class::$name);
    }

    /**
     * Unset a static property
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $class = $this->getEntity();
        unset($class::$name);
    }
}
