<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator\TestAsset\Cache;

use stdClass;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\Adapter\MemoryOptions;
use Zend\Cache\Storage\Capabilities;

/**
 * Class BasicCacheAdapter
 *
 * A minimal cache adapter based on Zend\Cache\Storage\Adapter\Memory
 * without implementing any cache capabilities interfaces
 * Used to test cache with Paginator.
 *
 * @package ZendTest\Paginator\TestAsset\Cache
 */
class BasicCacheAdapter extends AbstractAdapter
{
    /**
     * Data Array
     *
     * Format:
     * array(
     *     <NAMESPACE> => array(
     *         <KEY> => array(
     *             0 => <VALUE>
     *             1 => <MICROTIME>
     *             ['tags' => <TAGS>]
     *         )
     *     )
     * )
     *
     * @var array
     */
    protected $data = array();

    /**
     * Set options.
     *
     * @param  array|\Traversable|MemoryOptions $options
     * @return TestCacheAdapter
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof MemoryOptions) {
            $options = new MemoryOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return MemoryOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new MemoryOptions());
        }
        return $this->options;
    }

    /* IterableInterface */

    /* reading */

    /**
     * Internal method to get an item.
     *
     * @param  string  $normalizedKey
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        $options = $this->getOptions();
        $ns      = $options->getNamespace();
        $success = isset($this->data[$ns][$normalizedKey]);
        if ($success) {
            $data = & $this->data[$ns][$normalizedKey];
            $ttl  = $options->getTtl();
            if ($ttl && microtime(true) >= ($data[1] + $ttl)) {
                $success = false;
            }
        }

        if (!$success) {
            return null;
        }

        $casToken = $data[0];
        return $data[0];
    }

    /**
     * Internal method to get multiple items.
     *
     * @param  array $normalizedKeys
     * @return array Associative array of keys and values
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItems(array & $normalizedKeys)
    {
        $options = $this->getOptions();
        $ns      = $options->getNamespace();
        if (!isset($this->data[$ns])) {
            return array();
        }

        $data = & $this->data[$ns];
        $ttl  = $options->getTtl();
        $now  = microtime(true);

        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if (isset($data[$normalizedKey])) {
                if (!$ttl || $now < ($data[$normalizedKey][1] + $ttl)) {
                    $result[$normalizedKey] = $data[$normalizedKey][0];
                }
            }
        }

        return $result;
    }

    /**
     * Internal method to test if an item exists.
     *
     * @param  string $normalizedKey
     * @return bool
     */
    protected function internalHasItem(& $normalizedKey)
    {
        $options = $this->getOptions();
        $ns      = $options->getNamespace();
        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        // check if expired
        $ttl = $options->getTtl();
        if ($ttl && microtime(true) >= ($this->data[$ns][$normalizedKey][1] + $ttl)) {
            return false;
        }

        return true;
    }

    /**
     * Internal method to test multiple items.
     *
     * @param array $normalizedKeys
     * @return array Array of found keys
     */
    protected function internalHasItems(array & $normalizedKeys)
    {
        $options = $this->getOptions();
        $ns      = $options->getNamespace();
        if (!isset($this->data[$ns])) {
            return array();
        }

        $data = & $this->data[$ns];
        $ttl  = $options->getTtl();
        $now  = microtime(true);

        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if (isset($data[$normalizedKey])) {
                if (!$ttl || $now < ($data[$normalizedKey][1] + $ttl)) {
                    $result[] = $normalizedKey;
                }
            }
        }

        return $result;
    }

    /**
     * Get metadata of an item.
     *
     * @param  string $normalizedKey
     * @return array|bool Metadata on success, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers getMetadata.pre(PreEvent)
     * @triggers getMetadata.post(PostEvent)
     * @triggers getMetadata.exception(ExceptionEvent)
     */
    protected function internalGetMetadata(& $normalizedKey)
    {
        if (!$this->internalHasItem($normalizedKey)) {
            return false;
        }

        $ns = $this->getOptions()->getNamespace();
        return array(
            'mtime' => $this->data[$ns][$normalizedKey][1],
        );
    }

    /* writing */

    /**
     * Internal method to store an item.
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $options = $this->getOptions();

        if (!$this->hasAvailableSpace()) {
            $memoryLimit = $options->getMemoryLimit();
            throw new Exception\OutOfSpaceException(
                "Memory usage exceeds limit ({$memoryLimit})."
            );
        }

        $ns = $options->getNamespace();
        $this->data[$ns][$normalizedKey] = array($value, microtime(true));

        return true;
    }

    /**
     * Internal method to store multiple items.
     *
     * @param  array $normalizedKeyValuePairs
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItems(array & $normalizedKeyValuePairs)
    {
        $options = $this->getOptions();

        if (!$this->hasAvailableSpace()) {
            $memoryLimit = $options->getMemoryLimit();
            throw new Exception\OutOfSpaceException(
                "Memory usage exceeds limit ({$memoryLimit})."
            );
        }

        $ns = $options->getNamespace();
        if (!isset($this->data[$ns])) {
            $this->data[$ns] = array();
        }

        $data = & $this->data[$ns];
        $now  = microtime(true);
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $data[$normalizedKey] = array($value, $now);
        }

        return array();
    }

    /**
     * Add an item.
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItem(& $normalizedKey, & $value)
    {
        $options = $this->getOptions();

        if (!$this->hasAvailableSpace()) {
            $memoryLimit = $options->getMemoryLimit();
            throw new Exception\OutOfSpaceException(
                "Memory usage exceeds limit ({$memoryLimit})."
            );
        }

        $ns = $options->getNamespace();
        if (isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        $this->data[$ns][$normalizedKey] = array($value, microtime(true));
        return true;
    }

    /**
     * Internal method to add multiple items.
     *
     * @param  array $normalizedKeyValuePairs
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItems(array & $normalizedKeyValuePairs)
    {
        $options = $this->getOptions();

        if (!$this->hasAvailableSpace()) {
            $memoryLimit = $options->getMemoryLimit();
            throw new Exception\OutOfSpaceException(
                "Memory usage exceeds limit ({$memoryLimit})."
            );
        }

        $ns = $options->getNamespace();
        if (!isset($this->data[$ns])) {
            $this->data[$ns] = array();
        }

        $result = array();
        $data   = & $this->data[$ns];
        $now    = microtime(true);
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (isset($data[$normalizedKey])) {
                $result[] = $normalizedKey;
            } else {
                $data[$normalizedKey] = array($value, $now);
            }
        }

        return $result;
    }

    /**
     * Internal method to replace an existing item.
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalReplaceItem(& $normalizedKey, & $value)
    {
        $ns = $this->getOptions()->getNamespace();
        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }
        $this->data[$ns][$normalizedKey] = array($value, microtime(true));

        return true;
    }

    /**
     * Internal method to replace multiple existing items.
     *
     * @param  array $normalizedKeyValuePairs
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalReplaceItems(array & $normalizedKeyValuePairs)
    {
        $ns = $this->getOptions()->getNamespace();
        if (!isset($this->data[$ns])) {
            return array_keys($normalizedKeyValuePairs);
        }

        $result = array();
        $data   = & $this->data[$ns];
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (!isset($data[$normalizedKey])) {
                $result[] = $normalizedKey;
            } else {
                $data[$normalizedKey] = array($value, microtime(true));
            }
        }

        return $result;
    }

    /**
     * Internal method to reset lifetime of an item
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalTouchItem(& $normalizedKey)
    {
        $ns = $this->getOptions()->getNamespace();

        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        $this->data[$ns][$normalizedKey][1] = microtime(true);
        return true;
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        $ns = $this->getOptions()->getNamespace();
        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        unset($this->data[$ns][$normalizedKey]);

        // remove empty namespace
        if (!$this->data[$ns]) {
            unset($this->data[$ns]);
        }

        return true;
    }

    /**
     * Internal method to increment an item.
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalIncrementItem(& $normalizedKey, & $value)
    {
        $ns   = $this->getOptions()->getNamespace();
        $data = & $this->data[$ns];
        if (isset($data[$normalizedKey])) {
            $data[$normalizedKey][0]+= $value;
            $data[$normalizedKey][1] = microtime(true);
            $newValue = $data[$normalizedKey][0];
        } else {
            // initial value
            $newValue             = $value;
            $data[$normalizedKey] = array($newValue, microtime(true));
        }

        return $newValue;
    }

    /**
     * Internal method to decrement an item.
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalDecrementItem(& $normalizedKey, & $value)
    {
        $ns   = $this->getOptions()->getNamespace();
        $data = & $this->data[$ns];
        if (isset($data[$normalizedKey])) {
            $data[$normalizedKey][0]-= $value;
            $data[$normalizedKey][1] = microtime(true);
            $newValue = $data[$normalizedKey][0];
        } else {
            // initial value
            $newValue             = -$value;
            $data[$normalizedKey] = array($newValue, microtime(true));
        }

        return $newValue;
    }

    /* status */

    /**
     * Internal method to get capabilities of this adapter
     *
     * @return Capabilities
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities === null) {
            $this->capabilityMarker = new stdClass();
                $this->capabilities = new Capabilities(
                $this,
                $this->capabilityMarker,
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => true,
                        'boolean'  => true,
                        'integer'  => true,
                        'double'   => true,
                        'string'   => true,
                        'array'    => true,
                        'object'   => true,
                        'resource' => true,
                    ),
                    'supportedMetadata'  => array('mtime'),
                    'minTtl'             => 1,
                    'maxTtl'             => PHP_INT_MAX,
                    'staticTtl'          => false,
                    'ttlPrecision'       => 0.05,
                    'expiredRead'        => true,
                    'maxKeyLength'       => 0,
                    'namespaceIsPrefix'  => false,
                    'namespaceSeparator' => '',
                )
            );
        }

        return $this->capabilities;
    }

    /* internal */

    /**
     * Has space available to store items?
     *
     * @return bool
     */
    protected function hasAvailableSpace()
    {
        $total = $this->getOptions()->getMemoryLimit();

        // check memory limit disabled
        if ($total <= 0) {
            return true;
        }

        $free = $total - (float) memory_get_usage(true);
        return ($free > 0);
    }
}
