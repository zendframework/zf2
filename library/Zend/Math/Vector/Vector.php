<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Math\Vector;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LengthException;

/**
 * A mathematical Vector.
 */
class Vector implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The data of this vector.
     *
     * @var array
     */
    private $data;

    /**
     * Initializes a new instance of this class.
     *
     * @param array $data The data to initialize with.
     */
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
            for ($i = 0; $i < count($this->data); ++$i) {
                $this->data[$i] += $value[$i];
            }
        } else {
            for ($i = 0; $i < count($this->data); ++$i) {
                $this->data[$i] += $value;
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
     * Divides this vector by a scalar.
     *
     * @param float $value The value to divide with.
     * @return Vector
     */
    public function divide($value)
    {
        for ($i = 0; $i < count($this->data); ++$i) {
            $this->data[$i] /= $value;
        }
        return $this;
    }

    /**
     * Gets the dimension of the Vector. This is the same as calling count() on the instance.
     *
     * @return int
     */
    public function getDimension()
    {
        return count($this->data);
    }

    /**
     * Calculates the cartesian distance between this vector and the given vector.
     *
     * @param Vector $vector The vector to calculate the distance to.
     * @return float
     */
    public function getDistance(Vector $vector)
    {
        if (count($this) != count($vector)) {
            throw new LengthException('The two given vectors must be of the same length.');
        }

        $result = 0;
        for ($i = 0; $i < count($this->data); ++$i) {
            $result += pow($this->data[$i] - $vector[$i], 2);
        }
        return sqrt($result);
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
        for ($i = 0; $i < count($this->data); ++$i) {
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
        for ($i = 0; $i < count($this->data); ++$i) {
            $this->data[$i] *= $value;
        }
        return $this;
    }

    /**
     * Negates the vector.
     */
    public function negate()
    {
        for ($i = 0; $i < count($this->data); ++$i) {
            $this->data[$i] *= -1;
        }
    }

    /**
     * Normalizes the vector.
     */
    public function normalize()
    {
        $length = $this->getLength();
        for ($i = 0; $i < count($this->data); ++$i) {
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
            for ($i = 0; $i < count($this->data); ++$i) {
                $this->data[$i] -= $value[$i];
            }
        } else {
            for ($i = 0; $i < count($this->data); ++$i) {
                $this->data[$i] -= $value;
            }
        }
        return $this;
    }

    /**
     * Checks if the given offset exists.
     *
     * @param int $offset The offset to check.
     * @return bool
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
     * Converts this vector to a flat array with data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
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
