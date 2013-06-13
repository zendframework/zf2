<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Traversable;
use Zend\Filter\Encrypt\EncryptionAdapterInterface;
use Zend\Filter\Encrypt\EncryptionAdapterPluginManager;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Encrypts a given string
 */
class Encrypt extends AbstractFilter
{
    /**
     * @var EncryptionAdapterPluginManager
     */
    protected $adapterPluginManager;

    /**
     * Encryption adapter
     *
     * @var EncryptionAdapterInterface
     */
    protected $adapter;

    /**
     * Compression adapter constructor options
     */
    protected $adapterOptions = array();

    /**
     * @param EncryptionAdapterPluginManager $adapterPluginManager
     * @param array|Traversable|null         $options
     */
    public function __construct(EncryptionAdapterPluginManager $adapterPluginManager, $options = null)
    {
        $this->adapterPluginManager = $adapterPluginManager;
        parent::__construct($options);
    }

    /**
     * Set the adapter (if a string, it is pulled from a encryption adapter plugin manager)
     *
     * @param string|EncryptionAdapterInterface $adapter
     * @return void
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            $adapter = $this->adapterPluginManager->get($adapter);
        }

        $this->adapter = $adapter;
    }

    /**
     * Get the current compression adapter
     *
     * @return EncryptionAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set adapter options
     *
     * @param  array $options
     * @return void
     */
    public function setAdapterOptions(array $options)
    {
        $this->adapterOptions = $options;
    }

    /**
     * Retrieve adapter options
     *
     * @return array
     */
    public function getAdapterOptions()
    {
        return $this->adapterOptions;
    }

    /**
     * Calls adapter methods
     *
     * @param string       $method  Method to call
     * @param string|array $options Options for this method
     * @return mixed
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, $options)
    {
        $part = substr($method, 0, 3);
        if ((($part != 'get') && ($part != 'set')) || !method_exists($this->adapter, $method)) {
            throw new Exception\BadMethodCallException("Unknown method '{$method}'");
        }

        return call_user_func_array(array($this->adapter, $method), $options);
    }

    /**
     * Encrypts the content $value with the defined settings
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $adapter = $this->getAdapter();
        if (($adapterOptions = $this->getAdapterOptions()) && $adapter instanceof AbstractOptions) {
            $adapter->setFromArray($adapterOptions);
        }

        return $this->adapter->encrypt($value);
    }
}
