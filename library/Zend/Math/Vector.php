<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Math;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use LengthException;

/**
 * A mathematical Vector.
 */
class Vector implements ArrayAccess, Countable, IteratorAggregate
{
    private $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Adds a scalar or Vector to this vector.
     *
     * @param float|Vector $value The value to add.
     * @return Vector
     */
    public function add($value)
    {
        if ($value instanceof Vector) {
            foreach ($this->data as $key => $element) {
                $this->data[$key] += $value[$key];
            }
        } else {
            foreach ($this->data as $key => $element) {
                $this->data[$key] += $value;
            }
        }
        return $this;
    }

    /**
     * Gets the amount of components that this vector has.
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

	/**
     * Calculates the cross product between the two given vectors.
     *
     * @param Vector $v1 The first vector to use in the calculation.
     * @param Vector $v2 The second vector to use in the calculation.
     * @return Vector
     */
    public static function crossProduct(Vector $v1, Vector $v2)
    {
        if (count($v1) != count($v2)) {
            throw new LengthException('The two given vectors must be of the same length.');
        }

        if (count($v1) != 3) {
            throw new LengthException('The cross product can only be calculated from a vector with a dimension of 3.');
        }

        $result = array(
            ($v1[1] * $v2[2]) - ($v1[2] * $v2[1]),
            ($v1[2] * $v2[0]) - ($v1[0] * $v2[2]),
            ($v1[0] * $v2[1]) - ($v1[1] * $v2[0]),
        );
        
        return new Vector($result);
    }

    /**
     * Divides this vector by a scalar.
     *
     * @param float $value The value to divide with.
     * @return Vector
     */
    public function divide($value)
    {
        foreach ($this->data as $key => $element) {
            $this->data[$key] /= $value;
        }
        return $this;
    }

	/**
     * Calculates the dot product between the two given vectors.
     *
     * @param Vector $v1 The first vector to use in the calculation.
     * @param Vector $v2 The second vector to use in the calculation.
     * @return float
     */
    public static function dotProduct(Vector $v1, Vector $v2)
    {
        if (count($v1) != count($v2)) {
            throw new LengthException('The two given vectors must be of the same length.');
        }

        $result = 0.0;
        foreach ($v1 as $k => $value) {
            $result += ($value * $v2[$k]);
        }
        return $result;
    }

    /**
     * Gets an external iterator of this vector.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Calculates the length of the vector.
     *
     * @return float
     */
    public function getLength()
    {
        return sqrt($this->getSquaredLength());
    }

    /**
     * This is an alias of getLength.
     *
     * @return float
     */
    public function getMagnitude()
    {
        return $this->getLength();
    }

    /**
     * Calculates the squared length of the vector.
     *
     * @return float
     */
    public function getSquaredLength()
    {
		$result = 0;
		for ($i = 0; $i < $this->count(); ++$i) {
			$result += pow($this->data[$i], 2);
        }
		return $result;
    }

    /**
     * Multiplies a scalar with this vector.
     *
     * @param float $value The value to multiply with.
     * @return Vector
     */
    public function multiply($value)
    {
        foreach ($this->data as $key => $element) {
            $this->data[$key] *= $value;
        }
        return $this;
    }

    /**
     * Negates the vector.
     */
    public function negate()
    {
        foreach ($this->data as $key => $element) {
            $this->data[$key] *= -1;
        }
    }

    /**
     * Normalizes the vector.
     */
    public function normalize()
    {
		$length = $this->getLength();
		for ($i = 0; $i < $this->count(); ++$i) {
			$this->data[$i] /= $length;
		}
    }

    /**
     * Subtracts a scalar or Vector from this vector.
     *
     * @param float|Vector $value The value to subtract.
     * @return Vector
     */
    public function subtract($value)
    {
        if ($value instanceof Vector) {
            foreach ($this->data as $key => $element) {
                $this->data[$key] -= $value[$key];
            }
        } else {
            foreach ($this->data as $key => $element) {
                $this->data[$key] -= $value;
            }
        }
        return $this;
    }

    /**
     * Checks
     * @param type $offset
     * @return type
     */
	public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

	/**
     * Retrieves the value located at the given offset.
     *
     * @param int $offset The offset of the value to get.
     * @return float
	 */
	public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * Sets the value for the given offset.
     *
     * @param int $offset The offset to set.
     * @param float $value The value to set.
     */
	public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

	/**
     * Unsets the given offset.
     *
     * @param int $offset The offset to unset.
     */
	public function offsetUnset($offset)
    {
        unset($this->data[$offset]);

        // Reset the keys:
        $this->data = array_values($this->data);
    }

	/**
	 * Converts this vector to a string.
	 *
	 * @return string
	 */
	public function toString()
	{
		return '[' . implode(',', $this->data) . ']';
	}

	/**
	 * Converts this vector to a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}
}
