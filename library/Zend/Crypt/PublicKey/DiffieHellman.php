<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\PublicKey;

use Zend\Crypt\Exception;
use Zend\Math;

/**
 * PHP implementation of the Diffie-Hellman public key encryption algorithm.
 * Allows two unassociated parties to establish a joint shared secret key
 * to be used in encrypting subsequent communications.
 */
class DiffieHellman
{
    const DEFAULT_KEY_SIZE = 2048;

    /**
     * Key formats
     */
    const FORMAT_BINARY = 'binary';
    const FORMAT_NUMBER = 'number';
    const FORMAT_BTWOC  = 'btwoc';

    /**
     * Static flag to select whether to use PHP5.3's openssl extension
     * if available.
     *
     * @var bool
     */
    public static $useOpenssl = true;

    /**
     * Default large prime number; required by the algorithm.
     *
     * @var string
     */
    private $prime = null;

    /**
     * The default generator number. This number must be greater than 0 but
     * less than the prime number set.
     *
     * @var string
     */
    private $generator = null;

    /**
     * A private number set by the local user. It's optional and will
     * be generated if not set.
     *
     * @var string
     */
    private $privateKey = null;

    /**
     * BigInteger support object courtesy of Zend\Math
     *
     * @var \Zend\Math\BigInteger\Adapter\AdapterInterface
     */
    private $math = null;

    /**
     * The public key generated by this instance after calling generateKeys().
     *
     * @var string
     */
    private $publicKey = null;

    /**
     * The shared secret key resulting from a completed Diffie Hellman
     * exchange
     *
     * @var string
     */
    private $secretKey = null;

    /**
     * @var resource
     */
    protected $opensslKeyResource = null;

    /**
     * Constructor; if set construct the object using the parameter array to
     * set values for Prime, Generator and Private.
     * If a Private Key is not set, one will be generated at random.
     *
     * @param string $prime
     * @param string $generator
     * @param string $privateKey
     * @param string $privateKeyFormat
     */
    public function __construct($prime, $generator, $privateKey = null, $privateKeyFormat = self::FORMAT_NUMBER)
    {
        $this->setPrime($prime);
        $this->setGenerator($generator);
        if ($privateKey !== null) {
            $this->setPrivateKey($privateKey, $privateKeyFormat);
        }

        // set up BigInteger adapter
        $this->math = Math\BigInteger\BigInteger::factory();
    }

    /**
     * Set whether to use openssl extension
     *
     * @static
     * @param bool $flag
     */
    public static function useOpensslExtension($flag = true)
    {
        static::$useOpenssl = (bool) $flag;
    }

    /**
     * Generate own public key. If a private number has not already been set,
     * one will be generated at this stage.
     *
     * @return DiffieHellman
     * @throws \Zend\Crypt\Exception\RuntimeException
     */
    public function generateKeys()
    {
        if (function_exists('openssl_dh_compute_key') && static::$useOpenssl !== false) {
            $details = array(
                'p' => $this->convert($this->getPrime(), self::FORMAT_NUMBER, self::FORMAT_BINARY),
                'g' => $this->convert($this->getGenerator(), self::FORMAT_NUMBER, self::FORMAT_BINARY)
            );
            if ($this->hasPrivateKey()) {
                $details['priv_key'] = $this->convert(
                    $this->privateKey, self::FORMAT_NUMBER, self::FORMAT_BINARY
                );
                $opensslKeyResource = openssl_pkey_new(array('dh' => $details));
            } else {
                $opensslKeyResource = openssl_pkey_new(array(
                    'dh'               => $details,
                    'private_key_bits' => self::DEFAULT_KEY_SIZE,
                    'private_key_type' => OPENSSL_KEYTYPE_DH
                ));
            }

            if (false === $opensslKeyResource) {
                throw new Exception\RuntimeException(
                    'Can not generate new key; openssl ' . openssl_error_string()
                );
            }

            $data = openssl_pkey_get_details($opensslKeyResource);

            $this->setPrivateKey($data['dh']['priv_key'], self::FORMAT_BINARY);
            $this->setPublicKey($data['dh']['pub_key'], self::FORMAT_BINARY);

            $this->opensslKeyResource = $opensslKeyResource;
        } else {
            // Private key is lazy generated in the absence of ext/openssl
            $publicKey = $this->math->powmod($this->getGenerator(), $this->getPrivateKey(), $this->getPrime());
            $this->setPublicKey($publicKey);
        }

        return $this;
    }

    /**
     * Setter for the value of the public number
     *
     * @param string $number
     * @param string $format
     * @return DiffieHellman
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function setPublicKey($number, $format = self::FORMAT_NUMBER)
    {
        $number = $this->convert($number, $format, self::FORMAT_NUMBER);
        if (!preg_match('/^\d+$/', $number)) {
            throw new Exception\InvalidArgumentException('Invalid parameter; not a positive natural number');
        }
        $this->publicKey = (string) $number;

        return $this;
    }

    /**
     * Returns own public key for communication to the second party to this transaction
     *
     * @param string $format
     * @return string
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function getPublicKey($format = self::FORMAT_NUMBER)
    {
        if ($this->publicKey === null) {
            throw new Exception\InvalidArgumentException(
                'A public key has not yet been generated using a prior call to generateKeys()'
            );
        }

        return $this->convert($this->publicKey, self::FORMAT_NUMBER, $format);
    }

    /**
     * Compute the shared secret key based on the public key received from the
     * the second party to this transaction. This should agree to the secret
     * key the second party computes on our own public key.
     * Once in agreement, the key is known to only to both parties.
     * By default, the function expects the public key to be in binary form
     * which is the typical format when being transmitted.
     *
     * If you need the binary form of the shared secret key, call
     * getSharedSecretKey() with the optional parameter for Binary output.
     *
     * @param string $publicKey
     * @param string $publicKeyFormat
     * @param string $secretKeyFormat
     * @return string
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     * @throws \Zend\Crypt\Exception\RuntimeException
     */
    public function computeSecretKey($publicKey, $publicKeyFormat = self::FORMAT_NUMBER,
                                                 $secretKeyFormat = self::FORMAT_NUMBER)
    {
        if (function_exists('openssl_dh_compute_key') && static::$useOpenssl !== false) {
            $publicKey = $this->convert($publicKey, $publicKeyFormat, self::FORMAT_BINARY);
            $secretKey = openssl_dh_compute_key($publicKey, $this->opensslKeyResource);
            if (false === $secretKey) {
                throw new Exception\RuntimeException(
                    'Can not compute key; openssl ' . openssl_error_string()
                );
            }
            $this->secretKey = $this->convert($secretKey, self::FORMAT_BINARY, self::FORMAT_NUMBER);
        } else {
            $publicKey = $this->convert($publicKey, $publicKeyFormat, self::FORMAT_NUMBER);
            if (!preg_match('/^\d+$/', $publicKey)) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter; not a positive natural number'
                );
            }
            $this->secretKey = $this->math->powmod($publicKey, $this->getPrivateKey(), $this->getPrime());
        }

        return $this->getSharedSecretKey($secretKeyFormat);
    }

    /**
     * Return the computed shared secret key from the DiffieHellman transaction
     *
     * @param string $format
     * @return string
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function getSharedSecretKey($format = self::FORMAT_NUMBER)
    {
        if (!isset($this->secretKey)) {
            throw new Exception\InvalidArgumentException(
                'A secret key has not yet been computed; call computeSecretKey() first'
            );
        }

        return $this->convert($this->secretKey, self::FORMAT_NUMBER, $format);
    }

    /**
     * Setter for the value of the prime number
     *
     * @param string $number
     * @return DiffieHellman
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function setPrime($number)
    {
        if (!preg_match('/^\d+$/', $number) || $number < 11) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter; not a positive natural number or too small: ' .
                'should be a large natural number prime'
            );
        }
        $this->prime = (string) $number;

        return $this;
    }

    /**
     * Getter for the value of the prime number
     *
     * @param string $format
     * @return string
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function getPrime($format = self::FORMAT_NUMBER)
    {
        if (!isset($this->prime)) {
            throw new Exception\InvalidArgumentException('No prime number has been set');
        }

        return $this->convert($this->prime, self::FORMAT_NUMBER, $format);
    }


    /**
     * Setter for the value of the generator number
     *
     * @param string $number
     * @return DiffieHellman
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function setGenerator($number)
    {
        if (!preg_match('/^\d+$/', $number) || $number < 2) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter; not a positive natural number greater than 1'
            );
        }
        $this->generator = (string) $number;

        return $this;
    }

    /**
     * Getter for the value of the generator number
     *
     * @param string $format
     * @return string
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function getGenerator($format = self::FORMAT_NUMBER)
    {
        if (!isset($this->generator)) {
            throw new Exception\InvalidArgumentException('No generator number has been set');
        }

        return $this->convert($this->generator, self::FORMAT_NUMBER, $format);
    }

    /**
     * Setter for the value of the private number
     *
     * @param string $number
     * @param string $format
     * @return DiffieHellman
     * @throws \Zend\Crypt\Exception\InvalidArgumentException
     */
    public function setPrivateKey($number, $format = self::FORMAT_NUMBER)
    {
        $number = $this->convert($number, $format, self::FORMAT_NUMBER);
        if (!preg_match('/^\d+$/', $number)) {
            throw new Exception\InvalidArgumentException('Invalid parameter; not a positive natural number');
        }
        $this->privateKey = (string) $number;

        return $this;
    }

    /**
     * Getter for the value of the private number
     *
     * @param string $format
     * @return string
     */
    public function getPrivateKey($format = self::FORMAT_NUMBER)
    {
        if (!$this->hasPrivateKey()) {
            $this->setPrivateKey($this->generatePrivateKey(), self::FORMAT_BINARY);
        }

        return $this->convert($this->privateKey, self::FORMAT_NUMBER, $format);
    }

    /**
     * Check whether a private key currently exists.
     *
     * @return bool
     */
    public function hasPrivateKey()
    {
        return isset($this->privateKey);
    }

    /**
     * Convert number between formats
     *
     * @param $number
     * @param string $inputFormat
     * @param string $outputFormat
     * @return string
     */
    protected function convert($number, $inputFormat = self::FORMAT_NUMBER,
                                        $outputFormat = self::FORMAT_BINARY)
    {
        if ($inputFormat == $outputFormat) {
            return $number;
        }

        // convert to number
        switch ($inputFormat) {
            case self::FORMAT_BINARY:
            case self::FORMAT_BTWOC:
                $number = $this->math->binToInt($number);
                break;
            case self::FORMAT_NUMBER:
            default:
                // do nothing
                break;
        }

        // convert to output format
        switch ($outputFormat) {
            case self::FORMAT_BINARY:
                return $this->math->intToBin($number);
                break;
            case self::FORMAT_BTWOC:
                return $this->math->intToBin($number, true);
                break;
            case self::FORMAT_NUMBER:
            default:
                return $number;
                break;
        }
    }

    /**
     * In the event a private number/key has not been set by the user,
     * or generated by ext/openssl, a best attempt will be made to
     * generate a random key. Having a random number generator installed
     * on linux/bsd is highly recommended! The alternative is not recommended
     * for production unless without any other option.
     *
     * @return string
     */
    protected function generatePrivateKey()
    {
        return Math\Rand::getBytes(strlen($this->getPrime()), true);
    }
}
