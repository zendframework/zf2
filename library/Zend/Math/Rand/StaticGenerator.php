<?php

namespace Zend\Math\Rand;

use Zend\Math\Rand\Generator;

class StaticGenerator
{
    /**
     * @var Generator
     */
    protected static $generator;

    /**
     * Get the static Random Number Generator
     *
     * @return Generator
     */
    protected static function getInstance()
    {
        if (!isset(self::$generator)) {
            self::$generator = new Generator();
        }
        return self::$generator;
    }

    /**
     * Generate random string of bytes of specified length
     *
     * @static
     * @param $length
     * @return string
     */
    public static function getBytes($length)
    {
        return static::getInstance()->getBytes($length);
    }

    /**
     * @static
     * @return bool
     */
    public static function getBoolean()
    {
        return static::getInstance()->getBoolean();
    }

    /**
     * @static
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function getInteger($min = 0, $max = PHP_INT_MAX)
    {
        return static::getInstance()->getInteger($min, $max);
    }

    /**
     * @static
     * @return float
     */
    public static function getFloat()
    {
        return static::getInstance()->getFloat();
    }

    /**
     * @static
     * @param int $length
     * @param string|null $charlist
     * @return string
     */
    public static function getString($length, $charlist = null)
    {
        return static::getInstance()->getString($length, $charlist);
    }

}
