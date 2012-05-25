<?php

namespace Zend\Math\Rand;

use Zend\Math\Rand\Exception;

class Generator
{
    const POOL_SIZE          = 320;
    const WRITES_BEFORE_MIX  = 16;

    /**
     * @var array
     */
    protected $sources = array();

    /**
     * @var string
     */
    protected $pool;

    /**
     * Randomness pool cursor position
     *
     * @var int
     */
    protected $poolCursorPos = 0;

    /**
     * Pool writes counter
     * @var int
     */
    protected $poolWriteCount = 0;

    /**
     * Constructor
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
            /* @var $source Closure */
            $source = $this->sources[$i];
            if ($s = $source($length)) {
                $this->pool .= $s;
            }
            $i = (++$i % $numSources);
        }

        $this->pool = substr($this->pool, 0, self::POOL_SIZE);

        // define mixing hash algorithm
        $this->hashAlgo = (mt_rand(0, 1) === 0) ? 'whirlpool' : 'sha512';
    }

    /**
     * Generate random string of bytes of specified length
     *
     * @param int $length
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function getBytes($length)
    {
        if ($length < 1 || $length > self::POOL_SIZE) {
            throw new Exception\InvalidArgumentException(
                'Length should be between 1 and ' . self::POOL_SIZE
            );
        }

        // collect entropy, and write to pool
        $size = (int) ceil($length / count($this->sources));

        /* @var $source Closure */
        foreach ($this->sources as $source) {
            if ($s = $source($size)) {
                $this->writeToPool($s);
            }
        }

        // read to buffer
        $rand = $this->readFromPool($length);

        // invert and mix pool
        $this->pool = ~$this->pool;
        $this->mix();

        // read to buffer
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
        return (ord($byte) + 1) % 2 ? true : false;
    }

    /**
     * Generate a random integer within given range.
     * Uses 0..PHP_INT_MAX if no range is given
     *
     * @param int $min
     * @param int $max
     * @return int
     * @throws Exception\InvalidArgumentException
     */
    public function getInteger($min = 0, $max = PHP_INT_MAX)
    {
        $tmp = (int) max($max, $min);
        $min = (int) min($max, $min);
        $max = $tmp;
        $range = $max - $min;
        if ($range == 0) {
            return $max;
        } elseif ($range > PHP_INT_MAX || is_float($range)) {
            throw new Exception\InvalidArgumentException('The supplied range is too big to generate');
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
        * 52-bits of significand precision, which is 6.5 bytes
        * we need to gather 7 bytes, and throw the last 4-bits away
        */
        $bytes = $this->getBytes(7);
        $bytes[6] = $bytes[6] & chr(0x0f);
        $bytes .= chr(0);

        // unpack two unsigned long (32-bit) = 64-bits = 7 bytes + the NULL byte
        list(, $a, $b) = unpack('V2', $bytes);

        // The second unsigned long has 20-bits of significant information
        return (float) ($a / pow(2.0, 52) + $b / pow(2.0, 20));
    }


    /**
     * Generate a random string of specified length.
     *
     * Use supplied character list for generating the new string,
     * or use Base 64 list if no character list provided.
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
            $bytes = $this->getBytes($numBytes);
            return substr(rtrim(base64_encode($bytes), '='), 0, $length);
        }

        $numBytes = ceil($length * ((log($listLen, 2) + 1) / 8));
        $bytes = $this->getBytes($numBytes);

        // convert to destination base
        $srcBase = 256;
        $dstBase = $listLen;

        $src = array_map('ord', str_split($bytes));
        $count = count($src);
        $dst = array();
        while ($count) {
            $itMax = $count;
            $rem = $count = $i = 0;
            while ($i < $itMax) {
                $dividend = $src[$i++] + $rem * $srcBase;
                $rem = $dividend % $dstBase;
                $res = ($dividend - $rem) / $dstBase;
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
            $this->sources[] = function ($length) {
                return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            };
        }
        // urandom device
        if (is_readable('/dev/urandom')) {
            $this->sources[] = function ($length) {
                $dev = fopen('/dev/urandom', 'rb');
                $rand = fread($dev, $length);
                fclose($dev);

                return $rand;
            };
        }
        // mt_rand()
        $this->sources[] = function ($length) {
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $result .= chr((mt_rand() ^ mt_rand()) % 256);
            }
            return $result;
        };
        // rand()
        $this->sources[] = function ($length) {
            $pid = getmypid();
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $result .= chr((rand() ^ $pid) % 256);
            }
            return $result;
        };
        // lcg
        $this->sources[] = function ($length) {
            $p1 = fmod(1000000 * lcg_value(), 0x7fffffff);
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $p2 = fmod(1000000 * lcg_value(), 0x7fffffff);
                $result .= chr(($p1 ^ $p2) % 256);
            }
            return $result;
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
            ++$this->poolWriteCount;
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
