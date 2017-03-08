<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Math\Vector;

/**
 * The VectorUtils class contains common operations that can be executed on Vectors.
 */
class VectorUtils
{
    /**
     * Adds the two given vectors to each other and return a new Vector.
     *
     * @param Vector $left The vector to calculate with.
     * @param Vector $right The vector to calculate with.
     * @return Vector
     */
    public static function add(Vector $left, Vector $right)
    {
        $result = new Vector($left->toArray());
        $result->add($right);
        return $result;
    }

    /**
     * Divides the Vector by a scalar and returns a new Vector.
     *
     * @param float $value The value to divide with.
     * @return Vector
     */
    public static function divide(Vector $vector, $value)
    {
        $result = new Vector($vector->toArray());
        $result->divide($value);
        return $this;
    }

    /**
     * Multiplies a scalar with the vector and returns a new Vector.
     *
     * @param float $value The value to multiply with.
     * @return Vector
     */
    public static function multiply(Vector $vector, $value)
    {
        $result = new Vector($vector->toArray());
        $result->multiply($value);
        return $this;
    }

    /**
     * Subtracts the two given vectors from each other and return a new Vector.
     *
     * @param Vector $left The vector to calculate with.
     * @param Vector $right The vector to calculate with.
     * @return Vector
     */
    public static function subtract(Vector $left, Vector $right)
    {
        $result = new Vector($left->toArray());
        $result->subtract($right);
        return $result;
    }

    /**
     * Creates a new Vector with the given dimension of which all values are set to 0.0
     *
     * @param int $dimension The dimension of the Vector to create.
     * @return Vector
     */
    public static function createZero($dimension)
    {
        return new Vector(array_fill(0, $dimension, 0.0));
    }

    /**
     * Creates a new Vector with the given dimension of which all values are set to 1.0
     *
     * @param int $dimension The dimension of the Vector to create.
     * @return Vector
     */
    public static function createOne($dimension)
    {
        return new Vector(array_fill(0, $dimension, 1.0));
    }

    /**
     * Calculates the cross product between the two given vectors.
     *
     * @param Vector $left The first vector to use in the calculation.
     * @param Vector $right The second vector to use in the calculation.
     * @return Vector
     */
    public static function crossProduct(Vector $left, Vector $right)
    {
        if (count($left) != count($right)) {
            throw new LengthException('The two given vectors must be of the same length.');
        }

        if (count($left) != 3) {
            throw new LengthException('The cross product can only be calculated from a vector with a dimension of 3.');
        }

        $result = array(
            ($left[1] * $right[2]) - ($left[2] * $right[1]),
            ($left[2] * $right[0]) - ($left[0] * $right[2]),
            ($left[0] * $right[1]) - ($left[1] * $right[0]),
        );

        return new Vector($result);
    }

    /**
     * Calculates the dot product between the two given vectors.
     *
     * @param Vector $left The first vector to use in the calculation.
     * @param Vector $right The second vector to use in the calculation.
     * @return float
     */
    public static function dotProduct(Vector $left, Vector $right)
    {
        if (count($left) != count($right)) {
            throw new LengthException('The two given vectors must be of the same length.');
        }

        $result = 0.0;
        foreach ($left as $k => $value) {
            $result += ($value * $right[$k]);
        }
        return $result;
    }

    /**
     * Calculates the cartesian distance between the two given Vectors.
     *
     * @param Vector $left The vector used to calculate with.
     * @param Vector $right The vector used to calculate with.
     * @return float
     */
    public static function distance(Vector $left, Vector $right)
    {
        $temp = new Vector($left->toArray());
        return $temp->getDistance($right);
    }

    /**
     * Negates the vector and returns a new instance.
     *
     * @param Vector The Vector to negate.
     * @return Vector
     */
    public static function negate(Vector $vector)
    {
        $result = new Vector($vector->toArray());
        $result->negate();
        return $result;
    }

    /**
     * Normalizes the given vector and returns a new instance.
     *
     * @param Vector The Vector to normalize.
     * @return Vector
     */
    public static function normalize(Vector $vector)
    {
        $result = new Vector($vector->toArray());
        $result->normalize();
        return $result;
    }
}
