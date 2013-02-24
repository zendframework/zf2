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
use IteratorAggregate;

/**
 * A mathematical Matrix.
 */
class Matrix implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The amount of rows that the matrix has.
     *
     * @var int
     */
    private $rows;

    /**
     * The amount of columns that the matrix has.
     *
     * @var int
     */
    private $columns;

    /**
     * The data of this matrix.
     *
     * @var array
     */
    private $data;

    /**
     * Initializes a new instance of this class.
     *
     * @param int $rows The amount of rows that the matrix has.
     * @param int $columns The amount of columns that the matrix has.
     * @param array $data The data to initialize with.
     */
    public function __construct($rows, $columns, array $data = array())
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->data = $data;
    }

    /**
     * Gets the amount of components that this matrix has.
     *
     * @return int
     */
    public function count()
    {
        return $this->rows * $this->columns;
    }

    /**
     * Gets the amount of columns that the matrix has.
     *
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columns;
    }

    /**
     * Gets the amount of rows that the matrix has.
     *
     * @return int
     */
    public function getRowCount()
    {
        return $this->rows;
    }

    /**
     * Calculates the determinant of the matrix.
     *
     * @return float
     */
    public function getDeterminant()
    {
    }

    /**
     * Gets an external iterator of this matrix.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Checks if this matrix is a square matrix.
     *
     * @return bool
     */
    public function isSquare()
    {
        return $this->rows == $this->columns;
    }

    /**
     * Makes an identity matrix out of this matrix.
     */
    public function makeIdentity()
    {
        for ($r = 0; $r < $this->rows; ++$r) {
            for ($c = 0; $c < $this->columns; ++$c) {
                $i = ($r * $this->columns) + $c;
                $this->data[$i] = $r == $c ? 1.0 : 0.0;
            }
        }
    }

    /**
     * Transposes the rows and columns of this matrix.
     */
    public function transpose()
    {
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
        return array_key_exists($offset, $this->data) ? $this->data[$offset] : 0;
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
     * Unsetting sets the value to zero.
     *
     * @param int $offset The offset to unset.
     */
    public function offsetUnset($offset)
    {
        $this->data[$offset] = 0;
    }

    /**
     * Converts this matrix to a string.
     *
     * @return string
     */
    public function toString()
    {
        $result = '[';
        for ($r = 0; $r < $this->rows; $r++) {
            $result .= '[';
            for ($c = 0; $c < $this->columns; $c++) {
                $i = ($r * $this->columns) + $c;
                if ($c > 0) {
                    $result .= ',';
                }
                $result .= $this->data[$i];
            }
            $result .= ']';
        }
        return $result . ']';
    }

    /**
     * Converts this matrix to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
