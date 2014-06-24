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
 * Encrypt using a symmetric cipher then authenticate using HMAC (SHA-256)
 */
class BlockCipher
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
    protected static $symmetricPlugins = null;

    /**
     * Hash algorithm for HMAC
     *
     * @var string
     */
    protected $hash = 'sha256';

    /**
     * Check if the salt has been set
     *
     * @var bool
     */
    protected $saltSetted = false;

    /**
     * The output is binary?
     *
     * @var bool
     */
    protected $binaryOutput = false;

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
     * @return BlockCipher
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
     * @return BlockCipher
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
     * @return BlockCipher
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
     * Set the salt (IV)
     *
     * @param  string                             $salt
     * @return BlockCipher
     * @throws Exception\InvalidArgumentException
     */
    public function setSalt($salt)
    {
        try {
            $this->cipher->setSalt($salt);
        } catch (Symmetric\Exception\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException("The salt is not valid: " . $e->getMessage());
        }
        $this->saltSetted = true;

        return $this;
    }

    /**
     * Get the salt (IV) according to the size requested by the algorithm
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->cipher->getSalt();
    }

    /**
     * Get the original salt value
     *
     * @return string
     */
    public function getOriginalSalt()
    {
        return $this->cipher->getOriginalSalt();
    }

    /**
     * Enable/disable the binary output
     *
     * @param  bool        $value
     * @return BlockCipher
     */
    public function setBinaryOutput($value)
    {
        $this->binaryOutput = (bool) $value;

        return $this;
    }

    /**
     * Get the value of binary output
     *
     * @return bool
     */
    public function getBinaryOutput()
    {
        return $this->binaryOutput;
    }

    /**
     * Set the encryption/decryption key
     *
     * @param  string$key
     * @return BlockCipher
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
     * @return BlockCipher
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
     * @return BlockCipher
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
     * @return BlockCipher
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
     * Encrypt then authenticate using HMAC
     *
     * @param  string                             $data
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function encrypt($data)
    {
        // 0 (as integer), 0.0 (as float) & '0' (as string) will return false, though these should be allowed
        // Must be a string, integer, or float in order to encrypt
        if ((is_string($data) && $data === '')
            || is_array($data)
            || is_object($data)
        ) {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }
        // Cast to string prior to encrypting
        if (!is_string($data)) {
            $data = (string) $data;
        }
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }

        if (!$this->saltSetted) {
            // generate a random salt (IV) if the salt has not been set
            $iv = Rand::getBytes($this->cipher->getSaltSize(), true);
        } else {
            $iv = $this->getSalt();
        }
        $keys = $this->generateKeys($iv);
        $this->cipher->setKey($keys['enc']);
        $this->cipher->setSalt($iv);

        // encryption
        $ciphertext = $this->cipher->encrypt($data);
        // HMAC
        $hmac = Hmac::compute($keys['hmac'],
                              $this->getHashAlgorithm(),
                              $this->cipher->getAlgorithm() . $ciphertext);
        if (!$this->binaryOutput) {
            $ciphertext = base64_encode($ciphertext);
        }

        return $hmac . $ciphertext;
    }

    /**
     * Encrypt then authenticate a file using HMAC
     *
     * @param  string                             $fileIn
     * @param  string                             $fileOut
     * @return boolean
     * @throws Exception\InvalidArgumentException
     */
    public function encryptFile($fileIn, $fileOut, $compress = true)
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
        $read = fopen($fileIn, "r");
        if ($compress) {
            // Compress the file to be encrypted
            $compressFile = $fileIn . '_zip';
            $write = fopen('compress.zlib://' . $compressFile, "w");
            while ($data = fread($read, $this->bufferSize)) {
                fwrite($write, $data);
            }
            fclose($write);
            fclose($read);
            $read = fopen($compressFile, "r");
        }
        $write = fopen($fileOut, "w");
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }

        if (!$this->saltSetted) {
            // generate a random salt (IV) if the salt has not been set
            $iv = Rand::getBytes($this->cipher->getSaltSize(), true);
        } else {
            $iv = $this->getSalt();
        }
        $keys  = $this->generateKeys($iv);
        $hmac  = '';
        $size  = 0;
        $tot   = filesize($fileIn);
        $this->cipher->setKey($keys['enc']);
        $padding = $this->cipher->getPadding();
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding);
        $this->cipher->setSalt($iv);

        $hashAlgo  = $this->getHashAlgorithm();
        $saltSize  = $this->cipher->getSaltSize();
        $algorithm = $this->cipher->getAlgorithm();

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
            $hmac = Hmac::compute($keys['hmac'],
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
        if ($compress) {
            unlink($compressFile);
        }

        return $result;
    }

    /**
     * Decrypt
     *
     * @param  string                             $data
     * @return string|bool
     * @throws Exception\InvalidArgumentException
     */
    public function decrypt($data)
    {
        if (!is_string($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt must be a string');
        }
        if ('' === $data) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }
        $hmacSize   = Hmac::getOutputSize($this->hash);
        $hmac       = substr($data, 0, $hmacSize);
        $ciphertext = substr($data, $hmacSize);
        if (!$this->binaryOutput) {
            $ciphertext = base64_decode($ciphertext);
        }
        $iv   = substr($ciphertext, 0, $this->cipher->getSaltSize());
        $keys = $this->generateKeys($iv);
        // set the decryption key
        $this->cipher->setKey($keys['enc']);
        $hmacNew = Hmac::compute($keys['hmac'],
                                 $this->hash,
                                 $this->cipher->getAlgorithm() . $ciphertext);
        if (!Utils::compareStrings($hmacNew, $hmac)) {
            return false;
        }

        return $this->cipher->decrypt($ciphertext);
    }

    /**
     * Decrypt a file
     *
     * @param  string                             $fileIn
     * @param  string                             $fileOut
     * @return boolean
     * @throws Exception\InvalidArgumentException
     */
    public function decryptFile($fileIn, $fileOut, $compress = true)
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
        $read = fopen($fileIn, "r");
        if ($compress) {
            $compressFile = $fileOut . "_zip";
            $write = fopen($compressFile, "w");
        } else {
            $write = fopen($fileOut, "w");
        }
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }

        $hmacRead = fread($read, Hmac::getOutputSize($this->getHashAlgorithm()));
        $iv       = fread($read, $this->cipher->getSaltSize());
        $tot      = filesize($fileIn);
        $hmac     = $iv;
        $size     = strlen($iv) + strlen($hmacRead);
        $keys     = $this->generateKeys($iv);
        $padding  = $this->cipher->getPadding();
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding);
        $this->cipher->setKey($keys['enc']);

        $blockSize = $this->cipher->getBlockSize();
        $hashAlgo  = $this->getHashAlgorithm();
        $algorithm = $this->cipher->getAlgorithm();
        $saltSize  = $this->cipher->getSaltSize();

        while ($data = fread($read, $this->bufferSize)) {
            $size += strlen($data);
            // Unpadding if last block
            if ($size + $blockSize >= $tot) {
                $this->cipher->setPadding($padding);
                $data .= fread($read, $blockSize);
            }
            $result = $this->cipher->decrypt($iv . $data);
            $hmac   = Hmac::compute($keys['hmac'],
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
            if ($compress) {
                unlink($compressFile);
            } else {
                unlink($fileOut);
            }

            return false;
        }
        // Uncompress the file out
        if ($compress) {
            $read  = fopen('compress.zlib://' . $compressFile, "r");
            $write = fopen($fileOut, "w");
            while ($data = fread($read, $this->bufferSize)) {
                fwrite($write, $data);
            }
            fclose($write);
            fclose($read);
            unlink($compressFile);
        }

        return true;
    }

    /**
     * Generate encryption key and authentication key
     * using PBKDF2 algorithm
     *
     * @param  string $iv
     * @return array
     */
    protected function generateKeys($iv)
    {
        if (!$this->getKey()) {
            throw new Exception\InvalidArgumentException('No key specified');
        }
        if (empty($this->cipher)) {
            throw new Exception\InvalidArgumentException('No symmetric cipher specified');
        }
        $key = Pbkdf2::calc($this->getPbkdf2HashAlgorithm(),
                            $this->getKey(),
                            $iv,
                            $this->getKeyIteration(),
                            $this->cipher->getKeySize() * 2);

        return array(
            'enc'  => substr($key, 0, $this->cipher->getKeySize()),
            'hmac' => substr($key, $this->cipher->getKeySize())
        );
    }
}
