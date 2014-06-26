<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt;

use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Crypt\Symmetric\SymmetricInterface;
use Zend\Math\Rand;

/**
 * Encrypt/decrypt a file using a symmetric cipher then authenticate using HMAC
 */
class FileCipher
{
    /**
     * Hash algorithm for Pbkdf2
     *
     * @var string
     */
    protected $pbkdf2Hash = 'sha256';

    /**
     * Symmetric cipher
     *
     * @var SymmetricInterface
     */
    protected $cipher;

    /**
     * Symmetric cipher plugin manager
     *
     * @var SymmetricPluginManager
     */
    protected static $symmetricPlugins;

    /**
     * Hash algorithm for HMAC
     *
     * @var string
     */
    protected $hash = 'sha256';

    /**
     * Number of iterations for Pbkdf2
     *
     * @var int
     */
    protected $keyIteration = 5000;

    /**
     * Key
     *
     * @var string
     */
    protected $key;

    /**
     * Buffer size for file encryption
     *
     * @var int
     */
    protected $bufferSize = 1048576; // 16 * 65536 bytes = 1 Mb

    /**
     * Constructor
     *
     * @param SymmetricInterface $cipher
     */
    public function __construct(SymmetricInterface $cipher)
    {
        $this->cipher = $cipher;
    }

    /**
     * Factory.
     *
     * @param  string      $adapter
     * @param  array       $options
     * @return self
     */
    public static function factory($adapter, $options = array())
    {
        $plugins = static::getSymmetricPluginManager();
        $adapter = $plugins->get($adapter, (array) $options);

        return new static($adapter);
    }

    /**
     * Returns the symmetric cipher plugin manager.  If it doesn't exist it's created.
     *
     * @return SymmetricPluginManager
     */
    public static function getSymmetricPluginManager()
    {
        if (static::$symmetricPlugins === null) {
            static::setSymmetricPluginManager(new SymmetricPluginManager());
        }

        return static::$symmetricPlugins;
    }

    /**
     * Set the symmetric cipher plugin manager
     *
     * @param  string|SymmetricPluginManager      $plugins
     * @throws Exception\InvalidArgumentException
     */
    public static function setSymmetricPluginManager($plugins)
    {
        if (is_string($plugins)) {
            if (!class_exists($plugins)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Unable to locate symmetric cipher plugins using class "%s"; class does not exist',
                    $plugins
                ));
            }
            $plugins = new $plugins();
        }
        if (!$plugins instanceof SymmetricPluginManager) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an instance or extension of %s\SymmetricPluginManager; received "%s"',
                __NAMESPACE__,
                (is_object($plugins) ? get_class($plugins) : gettype($plugins))
            ));
        }
        static::$symmetricPlugins = $plugins;
    }

    /**
     * Set the symmetric cipher
     *
     * @param  SymmetricInterface $cipher
     * @return self
     */
    public function setCipher(SymmetricInterface $cipher)
    {
        $this->cipher = $cipher;

        return $this;
    }

    /**
     * Get symmetric cipher
     *
     * @return SymmetricInterface
     */
    public function getCipher()
    {
        return $this->cipher;
    }

    /**
     * Set the number of iterations for Pbkdf2
     *
     * @param  int         $num
     * @return self
     */
    public function setKeyIteration($num)
    {
        $this->keyIteration = (int) $num;

        return $this;
    }

    /**
     * Get the number of iterations for Pbkdf2
     *
     * @return int
     */
    public function getKeyIteration()
    {
        return $this->keyIteration;
    }

    /**
     * Set the encryption/decryption key
     *
     * @param  string                             $key
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setKey($key)
    {
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set algorithm of the symmetric cipher
     *
     * @param  string                             $algo
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setCipherAlgorithm($algo)
    {
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }
        try {
            $this->cipher->setAlgorithm($algo);
        } catch (Symmetric\Exception\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        return $this;
    }

    /**
     * Get the cipher algorithm
     *
     * @return string|bool
     */
    public function getCipherAlgorithm()
    {
        if (!empty($this->cipher)) {
            return $this->cipher->getAlgorithm();
        }

        return false;
    }

    /**
     * Get the supported algorithms of the symmetric cipher
     *
     * @return array
     */
    public function getCipherSupportedAlgorithms()
    {
        if (!empty($this->cipher)) {
            return $this->cipher->getSupportedAlgorithms();
        }

        return array();
    }

    /**
     * Set the hash algorithm for HMAC authentication
     *
     * @param  string                             $hash
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setHashAlgorithm($hash)
    {
        if (!Hash::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "The specified hash algorithm '{$hash}' is not supported by Zend\Crypt\Hash"
            );
        }
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get the hash algorithm for HMAC authentication
     *
     * @return string
     */
    public function getHashAlgorithm()
    {
        return $this->hash;
    }

    /**
     * Set the hash algorithm for the Pbkdf2
     *
     * @param  string                             $hash
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setPbkdf2HashAlgorithm($hash)
    {
        if (!Hash::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "The specified hash algorithm '{$hash}' is not supported by Zend\Crypt\Hash"
            );
        }
        $this->pbkdf2Hash = $hash;

        return $this;
    }

    /**
     * Get the Pbkdf2 hash algorithm
     *
     * @return string
     */
    public function getPbkdf2HashAlgorithm()
    {
        return $this->pbkdf2Hash;
    }
    
    /**
     * Encrypt then authenticate a file using HMAC
     *
     * @param  string                             $fileIn
     * @param  string                             $fileOut
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function encrypt($fileIn, $fileOut)
    {
        if (!file_exists($fileIn)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "I cannot open the %s file", $fileIn
            ));
        }
        if (file_exists($fileOut)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "The file %s already exists", $fileOut
            ));
        }
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for encryption');
        }

        $read    = fopen($fileIn, "r");
        $write   = fopen($fileOut, "w");
        $iv      = Rand::getBytes($this->cipher->getSaltSize(), true);
        $keys    = Pbkdf2::calc($this->getPbkdf2HashAlgorithm(),
                                $this->getKey(),
                                $iv,
                                $this->getKeyIteration(),
                                $this->cipher->getKeySize() * 2);
        $hmac    = '';
        $size    = 0;
        $tot     = filesize($fileIn);
        $padding = $this->cipher->getPadding();

        $this->cipher->setKey(substr($keys, 0, $this->cipher->getKeySize()));
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding);
        $this->cipher->setSalt($iv);

        $hashAlgo  = $this->getHashAlgorithm();
        $saltSize  = $this->cipher->getSaltSize();
        $algorithm = $this->cipher->getAlgorithm();
        $keyHmac   = substr($keys, $this->cipher->getKeySize());

        while ($data = fread($read, $this->bufferSize)) {
            $size += strlen($data);
            // Padding if last block
            if ($size == $tot) {
                $this->cipher->setPadding($padding);
            }
            $result = $this->cipher->encrypt($data);
            if ($size <= $this->bufferSize) {
                // Write a placeholder for the HMAC and write the IV
                fwrite($write, str_repeat(0, Hmac::getOutputSize($hashAlgo)));
            } else {
                $result = substr($result, $saltSize);
            }
            $hmac = Hmac::compute($keyHmac,
                                  $hashAlgo,
                                  $algorithm . $hmac . $result);
            $this->cipher->setSalt(substr($result, -1 * $saltSize));
            if (fwrite($write, $result) !== strlen($result)) {
                return false;
            }
        }
        $result = true;
        // write the HMAC at the beginning of the file
        fseek($write, 0);
        if (fwrite($write, $hmac) !== strlen($hmac)) {
            $result = false;
        }
        fclose($write);
        fclose($read);

        return $result;
    }

    /**
     * Decrypt a file
     *
     * @param  string                             $fileIn
     * @param  string                             $fileOut
     * @param  bool                               $compress
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function decrypt($fileIn, $fileOut)
    {
        if (!file_exists($fileIn)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "I cannot open the %s file", $fileIn
            ));
        }
        if (file_exists($fileOut)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "The file %s already exists", $fileOut
            ));
        }
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for decryption');
        }

        $read     = fopen($fileIn, "r");
        $write    = fopen($fileOut, "w");
        $hmacRead = fread($read, Hmac::getOutputSize($this->getHashAlgorithm()));
        $iv       = fread($read, $this->cipher->getSaltSize());
        $tot      = filesize($fileIn);
        $hmac     = $iv;
        $size     = strlen($iv) + strlen($hmacRead);
        $keys     = Pbkdf2::calc($this->getPbkdf2HashAlgorithm(),
                                 $this->getKey(),
                                 $iv,
                                 $this->getKeyIteration(),
                                 $this->cipher->getKeySize() * 2);
        $padding  = $this->cipher->getPadding();
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding);
        $this->cipher->setKey(substr($keys, 0, $this->cipher->getKeySize()));

        $blockSize = $this->cipher->getBlockSize();
        $hashAlgo  = $this->getHashAlgorithm();
        $algorithm = $this->cipher->getAlgorithm();
        $saltSize  = $this->cipher->getSaltSize();
        $keyHmac   = substr($keys, $this->cipher->getKeySize());

        while ($data = fread($read, $this->bufferSize)) {
            $size += strlen($data);
            // Unpadding if last block
            if ($size + $blockSize >= $tot) {
                $this->cipher->setPadding($padding);
                $data .= fread($read, $blockSize);
            }
            $result = $this->cipher->decrypt($iv . $data);
            $hmac   = Hmac::compute($keyHmac,
                                    $hashAlgo,
                                    $algorithm . $hmac . $data);
            $iv     = substr($data, -1 * $saltSize);
            if (fwrite($write, $result) !== strlen($result)) {
                return false;
            }
        }
        fclose($write);
        fclose($read);

        // check for data integrity
        if (!Utils::compareStrings($hmac, $hmacRead)) {
            unlink($fileOut);
            return false;
        }
        return true;
    }
}
