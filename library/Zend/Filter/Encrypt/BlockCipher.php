<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Encrypt;

use Traversable;
use Zend\Filter\Compress\CompressionAdapterPluginManager;
use Zend\Filter\Exception;
use Zend\Stdlib\AbstractOptions;
use Zend\Crypt\BlockCipher as CryptBlockCipher;
use Zend\Crypt\Exception as CryptException;
use Zend\Crypt\Symmetric\Exception as SymmetricException;

/**
 * Encryption adapter for Zend\Crypt\BlockCipher
 */
class BlockCipher extends AbstractEncryptionAdapter
{
    /**
     * Definitions for encryption
     *
     * array(
     *     'key'           => encryption key string
     *     'key_iteration' => the number of iterations for the PBKDF2 key generation
     *     'algorithm      => cipher algorithm to use
     *     'hash'          => algorithm to use for the authentication
     *     'vector'        => initialization vector
     * )
     */
    protected $encryption = array(
        'key_iteration'       => 5000,
        'algorithm'           => 'aes',
        'hash'                => 'sha256',
    );

    /**
     * BlockCipher
     *
     * @var BlockCipher
     */
    protected $blockCipher;

    /**
     * Class constructor
     *
     * @param  CompressionAdapterPluginManager $compressionAdapterPluginManager
     * @param  string|array|Traversable $options Encryption Options
     * @throws Exception\RuntimeException
     */
    public function __construct(CompressionAdapterPluginManager $compressionAdapterPluginManager, $options = null)
    {
        try {
            $this->blockCipher = CryptBlockCipher::factory('mcrypt', $this->encryption);
        } catch (SymmetricException\RuntimeException $e) {
            throw new Exception\RuntimeException('The BlockCipher cannot be used without the Mcrypt extension');
        }

        parent::__construct($compressionAdapterPluginManager, $options);
    }

    /**
     * Sets new encryption options
     *
     * @param  string|array $options Encryption options
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setEncryption($options)
    {
        if (is_string($options)) {
            $this->blockCipher->setKey($options);
            $this->encryption['key'] = $options;

            return;
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options argument provided to filter');
        }

        $options = $options + $this->encryption;

        if (isset($options['key'])) {
            $this->setKey($options['key']);
        }

        if (isset($options['algorithm'])) {
            $this->setAlgorithm($options['algorithm']);
        }

        if (isset($options['hash'])) {
            $this->setHash($options['hash']);
        }

        if (isset($options['vector'])) {
            $this->setVector($options['vector']);
        }

        if (isset($options['key_iteration'])) {
            $this->blockCipher->setKeyIteration($options['key_iteration']);
        }

        $this->encryption = $options;
    }

    /**
     * Returns the set encryption options
     *
     * @return array
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * Set the algorithm
     *
     * @param  string $algorithm
     * @throws Exception\InvalidArgumentException
     */
    public function setAlgorithm($algorithm)
    {
        try {
            $this->blockCipher->setCipherAlgorithm($algorithm);
        } catch (CryptException\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException("The algorithm '$algorithm' is not supported");
        }

        $this->encryption['algorithm'] = $algorithm;
    }

    /**
     * Get the algorithm
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->encryption['algorithm'];
    }

    /**
     * Set the hash used for authentication
     *
     * @param  string $hash
     * @throws Exception\InvalidArgumentException
     */
    public function setHash($hash)
    {
        try {
            $this->blockCipher->setHashAlgorithm($hash);
        } catch (CryptException\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException("The hash '$hash' is not supported");
        }
    }

    /**
     * Get the hash used for authentication
     *
     * @return string
     */
    public function getHash()
    {
        return $this->encryption['hash'];
    }

    /**
     * Set the initialization vector
     *
     * @param  string $vector
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setVector($vector)
    {
        try {
            $this->blockCipher->setSalt($vector);
        } catch (CryptException\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        $this->encryption['vector'] = $vector;
    }

    /**
     * Returns the initialization vector
     *
     * @return string
     */
    public function getVector()
    {
        return $this->encryption['vector'];
    }

    /**
     * Set the encryption key
     *
     * @param  string $key
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setKey($key)
    {
        try {
            $this->blockCipher->setKey($key);
        } catch (CryptException\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        $this->encryption['key'] = $key;
    }

    /**
     * Get the encryption key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->encryption['key'];
    }

    /**
     * {@inheritDoc}
     */
    public function encrypt($value)
    {
        // Compress prior to encryption
        if (null !== $this->compression) {
            $value = $this->compression->compress($value);
        }

        try {
            $encrypted = $this->blockCipher->encrypt($value);
        } catch (CryptException\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        return $encrypted;
    }

    /**
     * {@inheritDoc}
     */
    public function decrypt($value)
    {
        try {
            $decrypted = $this->blockCipher->decrypt($value);
        } catch (CryptException\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        // decompress after decryption
        if (null !== $this->compression) {
            $decrypted = $this->compression->decompress($decrypted);
        }

        return $decrypted;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return 'BlockCipher';
    }
}
