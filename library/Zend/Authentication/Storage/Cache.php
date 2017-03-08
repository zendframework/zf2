<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Authentication\Storage;

use Zend\Authentication\Storage\StorageInterface as AuthStorageInterface;
use Zend\Cache\Storage\StorageInterface as CacheStorageInterface;

class Cache implements AuthStorageInterface
{
    /**
     * Default cache namespace
     */
    const NAMESPACE_DEFAULT = 'Zend_Auth';

    /**
     * Cache adapter to mimic PHP session storage
     * 
     * @var CacheStorageInterface
     */
    protected $cache;

    /**
     * Cache namespace
     * 
     * @var mixed
     */
    protected $namespace = self::NAMESPACE_DEFAULT;

    /**
     * Cache key name for auth data
     * 
     * @var mixed
     */
    protected $key;

    /**
     * Sets cache storage options and initializes cache namespace object
     * 
     * @param mixed $namespace 
     * @param mixed $key 
     * @param CacheStorageInterface $cache 
     */
    public function __construct(CacheStorageInterface $cache, $namespace = null, $key = null)
    {
        $this->cache = $cache;

        if ($namespace !== null) {
            $this->setNamespace($namespace);
        }
        if ($key !== null) {
            $this->key = $key;
        } elseif (session_id() !== '') {
            $this->key = session_id();
        } else {
            session_start();
            $this->key = session_id();
        }
    }

    /**
     * Defined by Zend\Authentication\Storage\StorageInterface
     * 
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->cache->hasItem($this->key);
    }

    /**
     * Defined by Zend\Authentication\Storage\StorageInterface
     * 
     * @return mixed
     */
    public function read()
    {
        return $this->cache->getItem($this->key);
    }

    /**
     * Defined by Zend\Authentication\Storage\StorageInterface
     * 
     * @param mixed $contents
     */
    public function write($contents)
    {
        $this->cache->setItem($this->key, $contents);
    }

    /**
     * Defined by Zend\Authentication\Storage\StorageInterface
     * 
     */
    public function clear()
    {
        $this->cache->removeItem($this->key);
    }

    /**
     * Returns the name of the cache key being set to and read from 
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the cache storage adapter
     * 
     * @return CacheStorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the cache namespace
     * 
     * @param string $namespace 
     */
    public function setNamespace($namespace)
    {
        $this->cache->getOptions()->setNamespace($namespace);
    }

    /**
     * Returns the cache namespace
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->cache->getOptions()->getNamespace();
    }
}
