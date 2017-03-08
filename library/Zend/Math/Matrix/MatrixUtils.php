<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Math\Matrix;

class MatrixUtils
{
    /**
     * Adds the two values to each other and returns a new Matrix.
     *
     * @param Matrix $left The matrix to calculate with.
     * @param scalar|Matrix $right The value to calculate with.
     * @return Matrix
     */
    public static function add(Matrix $left, $right)
    {
        $result = new Matrix($left->getNbRows(), $left->getNbColumns(), $left->toArray());
        $result->add($right);
        return $result;
    }

    /**
     * Divides the two given values by each other.
     *
     * @param Matrix $left The matrix to calculate with.
     * @param scalar $right The value to calculate with.
     * @return Matrix
     */
    public static function divide(Matrix $matrix, $scalar)
    {
        $result = new Matrix($matrix->getNbRows(), $matrix->getNbColumns(), $matrix->toArray());
        $result->divide($scalar);
        return $result;
    }

    /**
     * Multiplies the given Matrix with the given value and returns a new Matrix.
     *
     * @param Matrix $left The matrix to multiply with.
     * @param scalar|Matrix $right The value to multiply with.
     * @return Matrix
     */
    public static function multiply(Matrix $left, $right)
    {
        $result = new Matrix($left->getNbRows(), $left->getNbColumns(), $left->toArray());
        $result->multiply($right);
        return $result;
    }

    /**
     * Subtracts the given Matrix or scalar from the other Matrix and returns a new Matrix.
     *
     * @param Matrix $left The matrix to subtract from.
     * @param scalar|Matrix $right The value to subtract.
     * @return Matrix
     */
    public static function subtract(Matrix $left, $right)
    {
        $result = new Matrix($left->getNbRows(), $left->getNbColumns(), $left->toArray());
        $result->subtract($right);
        return $result;
    }

    /**
     * Creates a new identity Matrix.
     *
     * @param int $rows The amount of rows that the Matrix has.
     * @param int $columns The amount of columns that the Matrix has.
     * @return Matrix
     */
    public static function createIdentity($rows, $columns)
    {
        $result = new Matrix($rows, $columns);
        $result->makeIdentity();
        return $result;
    }

    /**
     * Creates a submatrix from the given Matrix.
     *
     * @param Matrix $matrix The matrix to create a submatrix from.
     * @param int $excludeRow The row to exclude.
     * @param int $excludeColumn The column to exclude.
     * @return Matrix
     */
    public static function createSubmatrix(Matrix $matrix, $excludeRow, $excludeColumn)
    {
        $result = new Matrix($matrix->getNbRows() - 1, $matrix->getNbColumns() - 1);

        $newRow = 0;
        foreach ($matrix->getRows() as $r => $row) {
            if ($r == $excludeRow) {
                continue;
            }

            $newColumn = 0;
            foreach ($row as $c => $value) {
                if ($c == $excludeColumn) {
                    continue;
                }

                $index = ($newRow * $result->getNbRows()) + $newColumn;
                $result[$index] = $value;

                $newColumn++;
            }

            $newRow++;
        }
        return $result;
    }

    /**
     * Creates a new Matrix with all zero values.
     *
     * @param int $rows The amount of rows that the Matrix has.
     * @param int $columns The amount of columns that the Matrix has.
     * @return Matrix
     */
    public static function createZero($rows, $columns)
    {
        $result = new Matrix($rows, $columns, array());
        $result->makeZero();
        return $result;
    }

    /**
     * Transposes the given matrix and returns a new Matrix.
     *
     * @param Matrix $matrix The matrix to transpose.
     * @return Matrix
     */
    public static function transpose(Matrix $matrix)
    {
        $result = new Matrix($matrix->getNbRows(), $matrix->getNbColumns(), $matrix->toArray());
        $result->transpose();
        return $result;
    }
}
