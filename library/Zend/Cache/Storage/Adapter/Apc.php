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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use APCIterator,
    ArrayObject,
    stdClass,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Capabilities;

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Storage
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Apc extends AbstractAdapter
{
    /**
     * Map selected properties on getDelayed & find
     * to APCIterator selector
     *
     * Init on constructor after ext/apc has been tested
     *
     * @var null|array
     */
    protected static $selectMap = null;

    /**
     * The used namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * Statement
     *
     * @var null|APCIterator
     */
    protected $stmtIterator = null;

    /**
     * Constructor
     *
     * @param  array $options Option
     * @throws Exception
     * @return void
     */
    public function __construct()
    {
        if (version_compare('3.1.6', phpversion('apc')) > 0) {
            throw new Exception\ExtensionNotLoadedException("Missing ext/apc >= 3.1.6");
        }

        $enabled = ini_get('apc.enabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('apc.enable_cli');
        }

        if (!$enabled) {
            throw new Exception\ExtensionNotLoadedException(
                "ext/apc is disabled - see 'apc.enabled' and 'apc.enable_cli'"
            );
        }

        // init select map
        if (static::$selectMap === null) {
            static::$selectMap = array(
                // 'key'       => \APC_ITER_KEY,
                'value'     => \APC_ITER_VALUE,
                'mtime'     => \APC_ITER_MTIME,
                'ctime'     => \APC_ITER_CTIME,
                'atime'     => \APC_ITER_ATIME,
                'rtime'     => \APC_ITER_DTIME,
                'ttl'       => \APC_ITER_TTL,
                'num_hits'  => \APC_ITER_NUM_HITS,
                'ref_count' => \APC_ITER_REFCOUNT,
                'mem_size'  => \APC_ITER_MEM_SIZE,

                // virtual keys
                'internal_key' => \APC_ITER_KEY,
            );
        }
    }

    /* options */

    /**
     * Set options.
     *
     * @param  array|Traversable|ApcOptions $options
     * @return ApcAdapter
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!is_array($options) 
            && !$options instanceof Traversable 
            && !$options instanceof ApcOptions
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, a Traversable object, or an ApcOptions instance; '
                . 'received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Get options.
     *
     * @return ApcOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new ApcOptions());
        }
        return $this->options;
    }


    /* reading */

    /**
     * Get an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return mixed Value on success and false on failure
     * @throws Exception
     *
     * @triggers getItem.pre(PreEvent)
     * @triggers getItem.post(PostEvent)
     * @triggers getItem.exception(ExceptionEvent)
     */
    public function getItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            $result      = apc_fetch($internalKey, $success);
            if (!$success) {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$internalKey}' not found");
                }
                $result = false;
            } else {
                if (array_key_exists('token', $options)) {
                    $options['token'] = $result;
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Assoziative array of existing keys and values or false on failure
     * @throws Exception
     *
     * @triggers getItems.pre(PreEvent)
     * @triggers getItems.post(PostEvent)
     * @triggers getItems.exception(ExceptionEvent)
     */
    public function getItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $namespaceSep = $baseOptions->getNamespaceSeparator();
            $internalKeys = array();
            foreach ($keys as $key) {
                $internalKeys[] = $options['namespace'] . $namespaceSep . $key;
            }

            $fetch = apc_fetch($internalKeys);
            if (!$options['ignore_missing_items']) {
                if (count($keys) != count($fetch)) {
                    $missing = implode("', '", array_diff($internalKeys, array_keys($fetch)));
                    throw new Exception\ItemNotFoundException('Keys not found: ' . $missing);
                }
            }

            // remove namespace prefix
            $prefixL = strlen($options['namespace'] . $namespaceSep);
            $result  = array();
            foreach ($fetch as $internalKey => &$value) {
                $result[ substr($internalKey, $prefixL) ] = $value;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Test if an item exists.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers hasItem.pre(PreEvent)
     * @triggers hasItem.post(PostEvent)
     * @triggers hasItem.exception(ExceptionEvent)
     */
    public function hasItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            $result      = apc_exists($internalKey);

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Test if an item exists.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers hasItems.pre(PreEvent)
     * @triggers hasItems.post(PostEvent)
     * @triggers hasItems.exception(ExceptionEvent)
     */
    public function hasItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $namespaceSep = $baseOptions->getNamespaceSeparator();
            $internalKeys = array();
            foreach ($keys as $key) {
                $internalKeys[] = $options['namespace'] . $namespaceSep . $key;
            }

            $exists  = apc_exists($internalKeys);
            $result  = array();
            $prefixL = strlen($options['namespace'] . $namespaceSep);
            foreach ($exists as $internalKey => $bool) {
                if ($bool === true) {
                    $result[] = substr($internalKey, $prefixL);
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get metadata of an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return array|boolean Metadata or false on failure
     * @throws Exception
     */
    public function getMetadata($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        $format   = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;
        $regexp   = '/^' . preg_quote($key, '/') . '$/';
        $it       = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
        $metadata = $it->current();

        // @see http://pecl.php.net/bugs/bug.php?id=22564
        if (!apc_exists($key)) {
            $metadata = false;
        }

        if (!$metadata) {
            if (!$options['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$key}' nout found");
            }

            return false;
        }

        $this->normalizeMetadata($metadata);
        return $metadata;
    }

    /**
     * Get all metadata for an item
     * 
     * @param  array $keys 
     * @param  array $options 
     * @return array
     * @throws Exception\ItemNotFoundException
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $nsl = strlen($options['namespace']);

        $namespaceSep = $baseOptions->getNamespaceSeparator();
        $keysRegExp   = array();
        foreach ($keys as &$key) {
            $keysRegExp[] = preg_quote($options['namespace'] . $namespaceSep . $key, '/');
        }
        $regexp = '/^(' . implode('|', $keysRegExp) . ')$/';

        $format = \APC_ITER_ALL ^ \APC_ITER_VALUE ^ \APC_ITER_TYPE;

        $it  = new APCIterator('user', $regexp, $format, 100, \APC_LIST_ACTIVE);
        $ret = array();
        foreach ($it as $internalKey => $metadata) {
            // @see http://pecl.php.net/bugs/bug.php?id=22564
            if (!apc_exists($internalKey)) {
                continue;
            }

            $this->normalizeMetadata($metadata);

            $key       = substr($internalKey, strpos($internalKey, $namespaceSep) + 1);
            $ret[$key] = & $metadata;
        }

        if (!$options['ignore_missing_items']) {
            if (count($keys) != count($ret)) {
                $missing = implode("', '", array_diff($keys, array_keys($ret)));
                throw new Exception\ItemNotFoundException('Keys not found: ' . $missing);
            }
        }

        return $ret;
    }

    /* writing */

    /**
     * Store an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed $value
     * @param  array $options
     * @return boolean
     * @throws Exception
     */
    public function setItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        if (!apc_store($key, $value, $options['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException("apc_store('{$key}', <{$type}>, {$options['ttl']}) failed");
        }

        return true;
    }

    /**
     * Store multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     */
    public function setItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);

        $keyValuePairs2 = array();
        foreach ($keyValuePairs as $key => &$value) {
            $keyValuePairs2[ $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key ] = &$value;
        }

        $errKeys = apc_store($keyValuePairs2, null, $options['ttl']);

        if ($errKeys) {
            throw new Exception\RuntimeException(
                "apc_store(<array>, null, {$options['ttl']}) failed for keys: "
                . "'" . implode("','", $errKeys) . "'"
            );
        }

        return true;
    }

    /**
     * Add an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     */
    public function addItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        if (!apc_add($key, $value, $options['ttl'])) {
            if (apc_exists($key)) {
                throw new Exception\RuntimeException("Key '{$key}' already exists");
            }

            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException("apc_add('{$key}', <{$type}>, {$options['ttl']}) failed");
        }

        return true;
    }

    /**
     * Add multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     */
    public function addItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);

        $keyValuePairs2 = array();
        foreach ($keyValuePairs as $key => &$value) {
            $keyValuePairs2[ $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key ] = &$value;
        }

        $errKeys = apc_add($keyValuePairs2, null, $options['ttl']);

        if ($errKeys) {
            throw new Exception\RuntimeException(
                "apc_add(<array>, null, {$options['ttl']}) failed for keys: "
                . "'" . implode("','", $errKeys) . "'"
            );
        }

        return true;
    }

    /**
     * Replace an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     */
    public function replaceItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        if (!apc_exists($key)) {
            throw new Exception\ItemNotFoundException("Key '{$key}' doesn't exist");
        }

        if (!apc_store($key, $value, $options['ttl'])) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new Exception\RuntimeException("apc_store('{$key}', <{$type}>, {$options['ttl']}) failed");
        }

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
     */
    public function removeItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $key = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        if (!apc_delete($key)) {
            if (!$options['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$key}' not found");
            }
        }

        return true;
    }

    /**
     * Remove multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws Exception
     */
    public function removeItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        foreach ($keys as &$key) {
            $key = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
        }

        $errKeys = apc_delete($keys);
        if ($errKeys) {
            if (!$options['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Keys '" . implode("','", $errKeys) . "' not found");
            }
        }

        return true;
    }

    /**
     * Increment an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  int $value
     * @param  array $options
     * @return int|boolean The new value of false on failure
     * @throws Exception
     */
    public function incrementItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        $value = (int)$value;
        $newValue = apc_inc($internalKey, $value);
        if ($newValue === false) {
            if (!apc_exists($internalKey)) {
                if ($options['ignore_missing_items']) {
                    $this->addItem($key, $value, $options);
                    $newValue = $value;
                } else {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$internalKey}' not found"
                    );
                }
            } else {
                throw new Exception\RuntimeException("apc_inc('{$internalKey}', {$value}) failed");
            }
        }

        return $newValue;
    }

    /**
     * Decrement an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  int $value
     * @param  array $options
     * @return int|boolean The new value or false or failure
     * @throws Exception
     */
    public function decrementItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $internalKey = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;

        $value = (int)$value;
        $newValue = apc_dec($internalKey, $value);
        if ($newValue === false) {
            if (!apc_exists($internalKey)) {
                if ($options['ignore_missing_items']) {
                    $this->addItem($key, -$value, $options);
                    $newValue = -$value;
                } else {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$internalKey}' not found"
                    );
                }
            } else {
                throw new Exception\RuntimeException("apc_inc('{$internalKey}', {$value}) failed");
            }
        }

        return $newValue;
    }

    /* non-blocking */

    /**
     * Get items that were marked to delay storage for purposes of removing blocking
     * 
     * @param  array $keys 
     * @param  array $options 
     * @return bool
     * @throws Exception
     */
    public function getDelayed(array $keys, array $options = array())
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        if (!$keys) {
            return true;
        }

        $this->normalizeOptions($options);

        $prefix = $options['namespace'] . $baseOptions->getNamespaceSeparator();
        $prefix = preg_quote($prefix, '/');

        $format = 0;
        foreach ($options['select'] as $property) {
            if (isset(self::$selectMap[$property])) {
                $format = $format | self::$selectMap[$property];
            }
        }

        $search = array();
        foreach ($keys as $key) {
            $search[] = preg_quote($key, '/');
        }
        $search = '/^' . $prefix . '(' . implode('|', $search) . ')$/';

        $this->stmtIterator = new APCIterator('user', $search, $format, 1, \APC_LIST_ACTIVE);
        $this->stmtActive   = true;
        $this->stmtOptions  = &$options;

        if (isset($options['callback'])) {
            $callback = $options['callback'];
            if (!is_callable($callback, false)) {
                $this->stmtActive   = false;
                $this->stmtIterator = null;
                $this->stmtOptions  = null;
                throw new Exception\InvalidArgumentException('Invalid callback');
            }

            while (($item = $this->fetch()) !== false) {
                call_user_func($callback, $item);
            }
        }

        return true;
    }

    /**
     * Find items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see fetch()
     * @see fetchAll()
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_ACTIVE, $options);
        if (($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE) {
            // This adapter doen't support to read expired items
            return true;
        }

        $prefix = $options['namespace'] . $baseOptions->getNamespaceSeparator();
        $search = '/^' . preg_quote($prefix, '/') . '+/';

        $format = 0;
        foreach ($options['select'] as $property) {
            if (isset(self::$selectMap[$property])) {
                $format = $format | self::$selectMap[$property];
            }
        }

        $this->stmtIterator = new APCIterator('user', $search, $format, 1, \APC_LIST_ACTIVE);
        $this->stmtActive   = true;
        $this->stmtOptions  = &$options;

        return true;
    }

    /**
     * Fetches the next item from result set
     *
     * @return array|boolean The next item or false
     * @see    fetchAll()
     */
    public function fetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        do {
            if (!$this->stmtIterator->valid()) {
                // clear stmt
                $this->stmtActive   = false;
                $this->stmtIterator = null;
                $this->stmtOptions  = null;

                return false;
            }

            // @see http://pecl.php.net/bugs/bug.php?id=22564
            $exist = apc_exists($this->stmtIterator->key());

            if ($exist) {
                $metadata = $this->stmtIterator->current();
                $this->normalizeMetadata($metadata);

                $select = $this->stmtOptions['select'];
                if (in_array('key', $select)) {
                    $internalKey = $this->stmtIterator->key();
                    $key = substr(
                        $internalKey, 
                        strpos($internalKey, $this->getOptions()->getNamespaceSeparator()) + 1
                    );
                    $metadata['key'] = $key;
                }
            }

            $this->stmtIterator->next();

        } while (!$exist);

        return $metadata;
    }

    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see clearByNamespace()
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $this->normalizeOptions($options);
        return $this->clearByRegEx('/.*/', $mode, $options);
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     * @see clear()
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $this->normalizeOptions($options);
        $prefix = $options['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $regex  = '/^' . preg_quote($prefix, '/') . '+/';

        return $this->clearByRegEx($regex, $mode, $options);
    }

    /* status */

    /**
     * Get capabilities
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
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
                    'supportedMetadata' => array(
                        'atime', 
                        'ctime', 
                        'internal_key',
                        'mem_size', 
                        'mtime', 
                        'num_hits', 
                        'ref_count', 
                        'rtime', 
                        'ttl',
                    ),
                    'maxTtl'             => 0,
                    'staticTtl'          => false,
                    'ttlPrecision'       => 1,
                    'useRequestTime'     => (bool) ini_get('apc.use_request_time'),
                    'expiredRead'        => false,
                    'maxKeyLength'       => 5182,
                    'namespaceIsPrefix'  => true,
                    'namespaceSeparator' => $this->getOptions()->getNamespaceSeparator(),
                    'iterable'           => true,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => true,
                )
            );
        }

        return $this->capabilities;
    }

    /**
     * Get storage capacity.
     *
     * @param  array $options
     * @return array|boolean Capacity as array or false on failure
     */
    public function getCapacity(array $options = array())
    {
        $mem = apc_sma_info(true);

        return array(
            'free'  => $mem['avail_mem'],
            'total' => $mem['num_seg'] * $mem['seg_size'],
        );
    }

    /* internal */

    /**
     * Clear cached items based on key regex
     * 
     * @param  string $regex 
     * @param  int $mode 
     * @param  array $options 
     * @return bool
     */
    protected function clearByRegEx($regex, $mode, array &$options)
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        if (($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE) {
            // no need to clear expired items
            return true;
        }

        return apc_delete(new APCIterator('user', $regex, 0, 1, \APC_LIST_ACTIVE));
    }

    /**
     * Normalize metadata to work with APC
     * 
     * @param  array $metadata 
     * @return void
     */
    protected function normalizeMetadata(array &$metadata)
    {
        // rename
        if (isset($metadata['creation_time'])) {
            $metadata['ctime'] = $metadata['creation_time'];
            unset($metadata['creation_time']);
        }

        if (isset($metadata['access_time'])) {
            $metadata['atime'] = $metadata['access_time'];
            unset($metadata['access_time']);
        }

        if (isset($metadata['deletion_time'])) {
            $metadata['rtime'] = $metadata['deletion_time'];
            unset($metadata['deletion_time']);
        }

        // remove namespace prefix
        if (isset($metadata['key'])) {
            $pos = strpos($metadata['key'], $this->getOptions()->getNamespaceSeparator());
            if ($pos !== false) {
                $metadata['internal_key'] = $metadata['key'];
            } else {
                $metadata['internal_key'] = $metadata['key'];
            }

            unset($metadata['key']);
        }
    }
}
