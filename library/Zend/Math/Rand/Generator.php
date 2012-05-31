<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @package Zend_Math
*/

namespace Zend\Math\Rand;

/**
 * Random Number Generator (RNG)
 *
 * @category   Zend
 * @package    Zend_Math
 * @subpackage Rand
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Generator
{
    /**
     * Size of randomness pool
     */
    const POOL_SIZE          = 320;

    /**
     * Number of writes before mixing
     */
    const WRITES_BEFORE_MIX  = 16;

    /**
     * Entropy sources
     *
     * @var array
     */
    protected $sources = array();

    /**
     * Randomness pool
     *
     * @var string
     */
    protected $pool;

    /**
     * Current position of the pool cursor
     *
     * @var int
     */
    protected $poolCursorPos = 0;

    /**
     * Pool writes counter
     *
     * @var int
     */
    protected $poolWriteCount = 0;

    /**
     * Pool mixing hashing algorithm
     *
     * @var string
     */
    protected $hashAlgo = 'sha512';

    /**
     * Constructor
     * Init entropy sources, create and fill the pool with initial random data,
     * define hash algorithm for mixing function
     */
    public function __construct()
    {
        // init entropy sources
        $this->initSources();

        // create randomness pool
        $this->pool = '';
        $numSources = count($this->sources);
        $length     = (int) ceil(self::POOL_SIZE / $numSources);

        $i = 0;
        while (strlen($this->pool) < self::POOL_SIZE) {
            /* @var Closure $source */
            $source = $this->sources[$i];
            if ($s = $source($length)) {
                $this->pool .= $s;
            }
            $i = (++$i % $numSources);
        }

        $this->pool = substr($this->pool, 0, self::POOL_SIZE);

        // randomly select mixing hash algorithm
        $this->hashAlgo = (mt_rand(0, 1) === 0) ? 'whirlpool' : 'sha512';
    }

    /**
     * Generate random string of bytes of specified length
     *
     * @param int $length
     * @return string
     * @throws Exception\DomainException
     */
    public function getBytes($length)
    {
        if ($length < 1 || $length > self::POOL_SIZE) {
            throw new Exception\DomainException(
                'Length should be between 1 and ' . self::POOL_SIZE
            );
        }

        // collect entropy, and write to pool
        $size = (int) ceil($length / count($this->sources));

        /* @var Closure $source */
        foreach ($this->sources as $source) {
            if ($s = $source($size)) {
                $this->writeToPool($s);
            }
        }

        $this->pool = substr($this->pool, 0, self::POOL_SIZE);

        // read to buffer
        $rand = $this->readFromPool($length);

        // invert and mix pool
        $this->pool = ~$this->pool;
        $this->mix();

        // XOR to buffer
        $rand ^= $this->readFromPool($length);

        return $rand;
    }

    /**
     * Generate random boolean
     *
     * @return bool
     */
    public function getBoolean()
    {
        $byte = $this->getBytes(1);
        return (boolean) ((ord($byte) + 1) % 2);
    }

    /**
     * Generate a random integer within given range.
     * Uses 0..PHP_INT_MAX if no range is given
     *
     * @param int $min The lowest value to return (default: 0)
     * @param int $max The highest value to return (default: PHP_INT_MAX)
     * @return int
     * @throws Exception\DomainException
     */
    public function getInteger($min = 0, $max = PHP_INT_MAX)
    {
        if ($min > $max) {
            throw new Exception\DomainException(
                'The min parameter must be lower than max parameter'
            );
        }

        $range = $max - $min;
        if ($range == 0) {
            return $max;
        } elseif ($range > PHP_INT_MAX || is_float($range)) {
            throw new Exception\DomainException('The supplied range is too big to generate');
        }

        return (int) ($min + round($this->getFloat() * $range));
    }

    /**
     * Generate random float (0..1)
     *
     * @return float
     */
    public function getFloat()
    {
        /**
        * PHP uses double precision floating-point format (64-bit)
        * 52-bits of significand precision
        * we need to gather 7 bytes, and throw the last 4-bits away
        */
        $bytes = $this->getBytes(7);
        $bytes[6] = $bytes[6] & chr(0x0f); // clear bits
        $bytes .= chr(0); // set 8th byte as NULL byte

        // unpack two unsigned long (32-bit) = 64-bits = 7 bytes + the NULL byte
        list(, $a, $b) = unpack('V2', $bytes);

        // The second unsigned long has 20-bits of significant information
        return (float) ($a / pow(2.0, 52) + $b / pow(2.0, 20));
    }

    /**
     * Generate a random string of specified length.
     *
     * Use supplied character list for generating the new string.
     * If no list provided - use Base 64 character set.
     *
     * @param int $length
     * @param null|string $charlist
     * @return string
     */
    public function getString($length, $charlist = null)
    {
        if ($length == 0) {
            return '';
        }
        $listLen = strlen($charlist);
        if ($listLen == 1) {
            return str_repeat($charlist, $length);
        } else if (empty($charlist)) {
            $numBytes = ceil($length * 0.75);
            $bytes    = $this->getBytes($numBytes);
            return substr(rtrim(base64_encode($bytes), '='), 0, $length);
        }

        $numBytes = ceil($length * ((log($listLen, 2) + 1) / 8));
        $bytes    = $this->getBytes($numBytes);

        // convert to destination base
        $srcBase = 256;
        $dstBase = $listLen;

        $src   = array_map('ord', str_split($bytes));
        $count = count($src);
        $dst   = array();
        while ($count) {
            $itMax = $count;
            $rem   = $count = $i = 0;
            while ($i < $itMax) {
                $dividend = $src[$i++] + $rem * $srcBase;
                $rem      = $dividend % $dstBase;
                $res      = ($dividend - $rem) / $dstBase;
                if ($count || $res) {
                    $src[$count++] = $res;
                }
            }
            $dst[] = $charlist[$rem];
        }

        return substr(implode(array_reverse($dst)), 0, $length);
    }

    /**
     * Init randomness sources
     */
    protected function initSources()
    {
        $this->sources = array();

        // openssl extension
        if (extension_loaded('openssl')) {
            $this->sources[] = function ($length) {
                return openssl_random_pseudo_bytes($length);
            };
        }
        // mcrypt extension
        if (extension_loaded('mcrypt')) {
            // PHP bug #55169
            // @link https://bugs.php.net/bug.php?id=55169
            if (strtoupper(substr(\PHP_OS, 0, 3)) !== 'WIN' || version_compare(\PHP_VERSION, '5.3.7') >= 0) {
                $this->sources[] = function ($length) {
                    $rand = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                    if ($rand !== false && strlen($rand) === $length) {
                        return $rand;
                    }
                    return false;
                };
            }
        }
        // /dev/urandom
        if (is_readable('/dev/urandom')) {
            $this->sources[] = function ($length) {
                $dev  = @fopen('/dev/urandom', 'rb');
                $rand = fread($dev, $length);
                fclose($dev);
                return $rand;
            };
        }
        // mt_rand()
        $this->sources[] = function ($length) {
            $rand = '';
            for ($i = 0; $i < $length; $i++) {
                $rand .= chr((mt_rand() ^ mt_rand()) % 256);
            }
            return $rand;
        };
        // rand()
        $this->sources[] = function ($length) {
            $seed = rand() ^ memory_get_usage();
            $rand = '';
            for ($i = 0; $i < $length; $i++) {
                $rand .= chr((rand() ^ $seed) % 256);
            }
            return $rand;
        };
        // LCG
        $this->sources[] = function ($length) {
            $seed = fmod(time() * getmypid(), 0x7FFFFFFF);
            $rand = '';
            for ($i = 0; $i < $length; $i++) {
                $p = fmod(1000000 * lcg_value(), 0x7FFFFFFF);
                $rand .= chr(($seed ^ $p) % 256);
            }
            return $rand;
        };

        shuffle($this->sources);
    }

    /**
     * Write bytes to pool
     *
     * @param string $bytes
     */
    protected function writeToPool($bytes)
    {
        $length = strlen($bytes);
        for ($i = 0; $i < $length; $i++) {
            $this->pool[$this->poolCursorPos] =
                chr((ord($this->pool[$this->poolCursorPos]) + ord($bytes[$i])) % 256);
            $this->movePoolCursor();
            $this->poolWriteCount++;
        }
        if ($this->poolWriteCount >= self::WRITES_BEFORE_MIX) {
            $this->poolWriteCount = 0;
            $this->mix();
        }
    }

    /**
     * Read bytes from pool
     *
     * @param int $length
     * @return string
     */
    protected function readFromPool($length)
    {
        $buffer = '';
        for ($i = 0; $i < $length; $i++) {
            $buffer .= $this->pool[$this->poolCursorPos];
            $this->movePoolCursor();
        }
        return $buffer;
    }

    /**
     * Advance pool cursor position
     *
     * @param int $step
     * @return int
     */
    protected function movePoolCursor($step = 1)
    {
        $this->poolCursorPos = ($this->poolCursorPos + $step) % self::POOL_SIZE;
        return $this->poolCursorPos;
    }

    /**
     * Mix pool using hash function
     */
    protected function mix()
    {
        $blockSize = 64;

        // this should not happen because pool has fixed size
        $r = self::POOL_SIZE % $blockSize;
        if ($r !== 0) {
            $this->pool = str_pad($this->pool, ($blockSize - $r), chr(0));
        }

        $numBlocks = self::POOL_SIZE / $blockSize;

        for ($i = 0; $i < $numBlocks; $i++) {
            $start = $i * $blockSize;
            $block = substr($this->pool, $start, $blockSize);
            $block ^= hash($this->hashAlgo, $this->pool, true);
            $this->pool = substr_replace($this->pool, $block, $start, $blockSize);
        }
    }
}
