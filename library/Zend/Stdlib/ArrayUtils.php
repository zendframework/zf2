<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib;

use Traversable;

/**
 * Utility class for testing and manipulation of PHP arrays.
 *
 * Declared abstract, as we have no need for instantiation.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 */
abstract class ArrayUtils
{
    /**
     * Test whether an array contains one or more string keys
     *
     * @param  mixed $value
     * @param  bool  $allowEmpty    Should an empty array() return true
     * @return bool
     */
    public static function hasStringKeys($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$value) {
            return $allowEmpty;
        }

        return count(array_filter(array_keys($value), 'is_string')) > 0;
    }

    /**
     * Test whether an array contains one or more integer keys
     *
     * @param  mixed $value
     * @param  bool  $allowEmpty    Should an empty array() return true
     * @return bool
     */
    public static function hasIntegerKeys($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$value) {
            return $allowEmpty;
        }

        return count(array_filter(array_keys($value), 'is_int')) > 0;
    }

    /**
     * Test whether an array contains one or more numeric keys.
     *
     * A numeric key can be one of the following:
     * - an integer 1,
     * - a string with a number '20'
     * - a string with negative number: '-1000'
     * - a float: 2.2120, -78.150999
     * - a string with float:  '4000.99999', '-10.10'
     *
     * @param  mixed $value
     * @param  bool  $allowEmpty    Should an empty array() return true
     * @return bool
     */
    public static function hasNumericKeys($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$value) {
            return $allowEmpty;
        }

        return count(array_filter(array_keys($value), 'is_numeric')) > 0;
    }

    /**
     * Test whether an array is a list
     *
     * A list is a collection of values assigned to continuous integer keys
     * starting at 0 and ending at count() - 1.
     *
     * For example:
     * <code>
     * $list = array( 'a','b','c','d' );
     * $list = array(
     *     0 => 'foo',
     *     1 => 'bar',
     *     2 => array( 'foo' => 'baz' ),
     * );
     * </code>
     *
     * @param  mixed $value
     * @param  bool  $allowEmpty    Is an empty list a valid list?
     * @return bool
     */
    public static function isList($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$value) {
            return $allowEmpty;
        }

        return (array_values($value) === $value);
    }

    /**
     * Test whether an array is a hash table.
     *
     * An array is a hash table if:
     *
     * 1. Contains one or more non-integer keys, or
     * 2. Integer keys are non-continuous or misaligned (not starting with 0)
     *
     * For example:
     * <code>
     * $hash = array(
     *     'foo' => 15,
     *     'bar' => false,
     * );
     * $hash = array(
     *     1995  => 'Birth of PHP',
     *     2009  => 'PHP 5.3.0',
     *     2012  => 'PHP 5.4.0',
     * );
     * $hash = array(
     *     'formElement,
     *     'options' => array( 'debug' => true ),
     * );
     * </code>
     *
     * @param  mixed $value
     * @param  bool  $allowEmpty    Is an empty array() a valid hash table?
     * @return bool
     */
    public static function isHashTable($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$value) {
            return $allowEmpty;
        }

        return (array_values($value) !== $value);
    }

    /**
     * Convert an iterator to an array.
     *
     * Converts an iterator to an array. The $recursive flag, on by default,
     * hints whether or not you want to do so recursively.
     *
     * @param  array|Traversable  $iterator     The array or Traversable object to convert
     * @param  bool               $recursive    Recursively check all nested structures
     * @throws Exception\InvalidArgumentException if $iterator is not an array or a Traversable object
     * @return array
     */
    public static function iteratorToArray($iterator, $recursive = true)
    {
        if (!is_array($iterator) && !$iterator instanceof Traversable) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable object');
        }

        if (!$recursive) {
            if (is_array($iterator)) {
                return $iterator;
            }

            return iterator_to_array($iterator);
        }

        if (method_exists($iterator, 'toArray')) {
            return $iterator->toArray();
        }

        $array = array();
        foreach ($iterator as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }

            if ($value instanceof Traversable) {
                $array[$key] = static::iteratorToArray($value, $recursive);
                continue;
            }

            if (is_array($value)) {
                $array[$key] = static::iteratorToArray($value, $recursive);
                continue;
            }

            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Merge two arrays together.
     *
     * If an integer key exists in both arrays, the value from the second array
     * will be appended the the first array. If both values are arrays, they
     * are merged together, else the value of the second array overwrites the
     * one of the first array.
     *
     * @param  array $a
     * @param  array $b
     * @param  string $remove Any array values set to this string in $b will be unset in $a
     * @return array
     */
    public static function merge(array $a, array $b, $remove = '__remove__')
    {
        $remove = (string) $remove;

        foreach ($b as $key => $value) {
            if (array_key_exists($key, $a)) {
                if (is_int($key)) {
                    $value != $remove ? $a[] = $value : null;
                } elseif (is_array($value) && is_array($a[$key])) {
                    $a[$key] = static::merge($a[$key], $value);
                } else {
                    if ($value == $remove){
                        unset($a[$key]);
                    } else {
                        $a[$key] = $value;
                    }
                }
            } else {
                $value != $remove ? $a[$key] = $value : null;
            }
        }

        return $a;
    }
}
