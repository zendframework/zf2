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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use ArrayObject,
    stdClass,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractZendServer extends AbstractAdapter
{
    /**
     * The namespace separator used on Zend Data Cache functions
     *
     * @var string
     */
    const NAMESPACE_SEPARATOR = '::';

    /* reading */

    /**
     * Internal method to get an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item or return false
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return mixed Data on success or false on failure
     * @throws Exception
     */
    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR . $normalizedKey;
        $result      = $this->zdcFetch($internalKey);
        if ($result === false) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
            }
        } elseif (array_key_exists('token', $normalizedOptions)) {
            $normalizedOptions['token'] = $result;
        }

        return $result;
    }

    /**
     * Internal method to get multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Associative array of existing keys and values
     * @throws Exception
     */
    protected function internalGetItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;

        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch   = $this->zdcFetchMulti($internalKeys);
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $k => & $v) {
            $result[ substr($k, $prefixL) ] = $v;
        }

        return $result;
    }

    /**
     * Internal method to test if an item exists.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalHasItem(& $normalizedKey, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;
        return  ($this->zdcFetch($prefix . $normalizedKey) !== false);
    }

    /**
     * Internal method to test multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of existing keys
     * @throws Exception
     */
    protected function internalHasItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;

        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch   = $this->zdcFetchMulti($internalKeys);
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $internalKey => & $value) {
            $result[] = substr($internalKey, $prefixL);
        }

        return $result;
    }

    /**
     * Get metadata for multiple items
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array|boolean
     *
     * @triggers getMetadatas.pre(PreEvent)
     * @triggers getMetadatas.post(PostEvent)
     * @triggers getMetadatas.exception(ExceptionEvent)
     */
    protected function internalGetMetadatas(array & $normalizedKeys, array & $normalizedOptions)
    {
        $prefix       = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR;
        $internalKeys = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $internalKeys[] = $prefix . $normalizedKey;
        }

        $fetch   = $this->zdcFetchMulti($internalKeys);
        $prefixL = strlen($prefix);
        $result  = array();
        foreach ($fetch as $internalKey => $value) {
            $result[ substr($internalKey, $prefixL) ] = array();
        }

        return $result;
    }

    /* writing */

    /**
     * Internal method to store an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $internalKey = $normalizedOptions['namespace'] . self::NAMESPACE_SEPARATOR . $normalizedKey;
        $this->zdcStore($internalKey, $value, $normalizedOptions['ttl']);
        return true;
    }

    /**
     * Remove an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItem.pre(PreEvent)
     * @triggers removeItem.post(PostEvent)
     * @triggers removeItem.exception(ExceptionEvent)
     */
    public function removeItem($key, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . self::NAMESPACE_SEPARATOR . $key;
            if (!$this->zdcDelete($internalKey) && !$options['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see clearByNamespace()
     *
     * @triggers clear.pre(PreEvent)
     * @triggers clear.post(PostEvent)
     * @triggers clear.exception(ExceptionEvent)
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            // clear all
            if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
                $this->zdcClear();
            }

            // expired items will be deleted automatic

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     * @see clear()
     *
     * @triggers clearByNamespace.pre(PreEvent)
     * @triggers clearByNamespace.post(PostEvent)
     * @triggers clearByNamespace.exception(ExceptionEvent)
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            // clear all
            if (($mode & self::MATCH_ACTIVE) == self::MATCH_ACTIVE) {
                $this->zdcClearByNamespace($options['namespace']);
            }

            // expired items will be deleted automatic

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* status */

    /**
     * Get capabilities
     *
     * @return Capabilities
     *
     * @triggers getCapabilities.pre(PreEvent)
     * @triggers getCapabilities.post(PostEvent)
     * @triggers getCapabilities.exception(ExceptionEvent)
     */
    public function getCapabilities()
    {
        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($this->capabilities === null) {
                $this->capabilityMarker = new stdClass();
                $this->capabilities     = new Capabilities(
                    $this->capabilityMarker,
                    array(
                        'supportedDatatypes' => array(
                            'NULL'     => true,
                            'boolean'  => true,
                            'integer'  => true,
                            'double'   => true,
                            'string'   => true,
                            'array'    => true,
                            'object'   => 'object',
                            'resource' => false,
                        ),
                        'supportedMetadata'  => array(),
                        'maxTtl'             => 0,
                        'staticTtl'          => true,
                        'tagging'            => false,
                        'ttlPrecision'       => 1,
                        'useRequestTime'     => false,
                        'expiredRead'        => false,
                        'maxKeyLength'       => 0,
                        'namespaceIsPrefix'  => true,
                        'namespaceSeparator' => self::NAMESPACE_SEPARATOR,
                        'iterable'           => false,
                        'clearAllNamespaces' => true,
                        'clearByNamespace'   => true,
                    )
                );
            }

            return $this->triggerPost(__FUNCTION__, $args, $this->capabilities);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* internal wrapper of zend_[disk|shm]_cache_* functions */

    /**
     * Store data into Zend Data Cache (zdc)
     *
     * @param  string $internalKey
     * @param  mixed  $value
     * @param  int    $ttl
     * @return void
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcStore($internalKey, $value, $ttl);

    /**
     * Fetch a single item from Zend Data Cache (zdc)
     *
     * @param  string $internalKey
     * @return mixed The stored value or FALSE if item wasn't found
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcFetch($internalKey);

    /**
     * Fetch multiple items from Zend Data Cache (zdc)
     *
     * @param  array $internalKeys
     * @return array All found items
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcFetchMulti(array $internalKeys);

    /**
     * Delete data from Zend Data Cache (zdc)
     *
     * @param  string $internalKey
     * @return boolean
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcDelete($internalKey);

    /**
     * Clear items of all namespaces from Zend Data Cache (zdc)
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcClear();

    /**
     * Clear items of the given namespace from Zend Data Cache (zdc)
     *
     * @param  string $namespace
     * @return void
     * @throws Exception\RuntimeException
     */
    abstract protected function zdcClearByNamespace($namespace);
}
