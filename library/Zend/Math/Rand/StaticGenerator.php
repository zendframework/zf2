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
     * @param int $length
     * @return string
     */
    public static function getBytes($length)
    {
        return static::getInstance()->getBytes($length);
    }

    /**
     * Generate random boolean
     *
     * @return bool
     */
    public static function getBoolean()
    {
        return static::getInstance()->getBoolean();
    }

    /**
     * Generate a random integer within given range.
     * Uses 0..PHP_INT_MAX if no range is given.
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function getInteger($min = 0, $max = PHP_INT_MAX)
    {
        return static::getInstance()->getInteger($min, $max);
    }

    /**
     * Generate random float (0..1)
     *
     * @return float
     */
    public static function getFloat()
    {
        return static::getInstance()->getFloat();
    }

    /**
     * Generate a random string of specified length.
     *
     * Use supplied character list for generating the new string.
     * If no list provided - use Base 64 alphabet.
     *
     * @param int $length
     * @param string|null $charlist
     * @return string
     */
    public static function getString($length, $charlist = null)
    {
        return static::getInstance()->getString($length, $charlist);
    }
}
