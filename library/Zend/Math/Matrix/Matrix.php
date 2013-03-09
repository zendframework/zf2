<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Math\Matrix;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use Zend\Math\Vector\Vector;

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
        $this->data = array_fill(0, $rows * $columns, 0);

        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Adds the given scalar or Matrix to this matrix.
     *
     * @param scalar|Matrix $value The value to add.
     * @return Matrix
     */
    public function add($value)
    {
        if ($value instanceof Matrix) {
            if ($this->rows != $value->getRowCount() || $this->columns != $value->getColumnCount()) {
                throw new InvalidArgumentException('The matrices should be of the same dimension');
            }

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
     * Gets the amount of components that this matrix has.
     *
     * @return int
     */
    public function count()
    {
        return $this->rows * $this->columns;
    }

    /**
     * Divides the matrix by the given scalar.
     *
     * @param float $scalar The scalar to divide by.
     * @return Matrix
     */
    public function divide($scalar)
    {
        if (!is_scalar($scalar)) {
            throw new InvalidArgumentException('Only divisions by scalars are supported.');
        }

        return $this->multiply(1 / $scalar);
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
     * Gets an array with the columns as Vectors.
     *
     * @return Vector[]
     */
    public function getColumns()
    {
        $result = array();

        for ($c = 0; $c < $this->columns; ++$c) {
            $data = array();
            for ($r = 0; $r < $this->rows; ++$r) {
                $i = ($r * $this->columns) + $c;
                $data[] = $this->data[$i];
            }
            $result[] = new Vector($data);
        }
        
        return $result;
    }

    /**
     * Gets an array with the rows as Vectors.
     *
     * @return Vector[]
     */
    public function getRows()
    {
        $result = array();

        for ($r = 0; $r < $this->rows; ++$r) {
            $data = array();
            for ($c = 0; $c < $this->columns; ++$c) {
                $i = ($r * $this->columns) + $c;
                $data[] = $this->data[$i];
            }
            $result[] = new Vector($data);
        }

        return $result;
    }

    private function createSubmatrix(array $data, $dimension, $excludeRow, $excludeColumn)
    {
        $submatrix = array();

        $newDimension = 0;
        for ($r = 0; $r < $dimension; ++$r) {
            if ($r == $excludeRow) {
                continue;
            }
            $newDimension++;
        }

        $newR = 0;
        $newC = 0;

        for ($r = 0; $r < $dimension; ++$r) {
            if ($r == $excludeRow) {
                continue;
            }

            $newC = 0;
            for ($c = 0; $c < $dimension; ++$c) {
                if ($c == $excludeColumn) {
                    continue;
                }

                $indexOld = ($r * $dimension) + $c;
                $indexNew = ($newR * $newDimension) + $newC;

                $submatrix[$indexNew] = $data[$indexOld];
                ++$newC;

            }

            ++$newR;
        }
        return $submatrix;
    }

    private function calculateDeterminant(array $data, $dimension = 0)
    {
        $determinant = 0.0;

        if ($dimension == 2) {
            $determinant = ($data[0] * $data[3]) - ($data[2] * $data[1]);
        } else if ($dimension == 3) {
            $determinant = ($data[0] * $data[4] * $data[8])
                + ($data[1] * $data[5] * $data[6])
                + ($data[2] * $data[3] * $data[7])
                - ($data[6] * $data[4] * $data[2])
                - ($data[7] * $data[5] * $data[0])
                - ($data[8] * $data[3] * $data[1]);
        } else {
			for ($i = 0; $i < $dimension; $i++) {
                $submatrix = $this->createSubmatrix($data, $dimension, 0, $i);
				$determinant += $data[$i] * ($i % 2 === 0 ? 1 : -1)
                    * $this->calculateDeterminant($submatrix, $dimension - 1);
			}
        }

        return $determinant;
    }

    /**
     * Calculates the determinant of the matrix.
     *
     * @return float
     */
    public function getDeterminant()
    {
        if (!$this->isSquare()) {
            throw new RuntimeException('Cannot calculate the determinant of a non square matrix.');
        }

        return $this->calculateDeterminant($this->data, $this->rows);
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
     * Resets the values of the Matrix to zero.
     */
    public function makeZero()
    {
        for ($i = 0; $i < $this->rows * $this->columns; ++$i) {
            $this->data[$i] = 0.0;
        }
    }

    /**
     * Multiplies the given scalar or Matrix with this matrix.
     *
     * @param scalar|Matrix $value The value to multiply with.
     * @return Matrix
     */
    public function multiply($value)
    {
        if ($value instanceof Matrix) {
            if ($this->rows != $value->getRowCount() || $this->columns != $value->getColumnCount()) {
                throw new InvalidArgumentException('The matrices should be of the same dimension');
            }

            $clone = clone $this;
            for ($r = 0; $r < $this->rows; ++$r) {
                for ($c = 0; $c < $this->columns; ++$c) {
                    $index = ($r * $this->columns) + $c;
                    $this->data[$index] = 0.0;

                    for ($tmp = 0; $tmp < $this->columns; ++$tmp) {
                        $index1 = ($r * $this->columns) + $tmp;
                        $index2 = ($tmp * $this->rows) + $c;

                        $this->data[$index] += ($clone[$index1] * $value[$index2]);
                    }
                }
            }
        } else {
            foreach ($this->data as $key => $element) {
                $this->data[$key] *= $value;
            }
        }
        return $this;
    }

    /**
     * Subtracts the given value from the matrix.
     *
     * @param scalar|Matrix $value The value to subtract.
     * @return Matrix
     */
    public function subtract($value)
    {
        if ($value instanceof Matrix) {
            if ($this->rows != $value->getRowCount() || $this->columns != $value->getColumnCount()) {
                throw new InvalidArgumentException('The matrices should be of the same dimension');
            }

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
     * Transposes the rows and columns of this matrix.
     *
     * @return Matrix
     */
    public function transpose()
    {
        $newData = array();
        for ($r = 0; $r < $this->rows; ++$r) {
            for ($c = 0; $c < $this->columns; ++$c) {
                $ir = ($r * $this->columns) + $c;
                $ic = ($c * $this->rows) + $r;
                $newData[$ic] = $this->data[$ir];
            }
        }

        ksort($newData);

        // Swap the columns and rows:
        $columns = $this->columns;
        $this->columns = $this->rows;
        $this->rows = $columns;

        // Set the new data:
        $this->data = $newData;
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
     * Converts the Matrix to a flat array with the data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
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
