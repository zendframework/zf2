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
    public static function add(Matrix $left, $right)
    {
        $result = new Matrix($left->getNbRows(), $left->getNbColumns(), $left->toArray());
        $result->add($right);
        return $result;
    }

    public static function divide(Matrix $matrix, $scalar)
    {
        $result = new Matrix($matrix->getNbRows(), $matrix->getNbColumns(), $matrix->toArray());
        $result->divide($scalar);
        return $result;
    }

    public static function multiply(Matrix $left, $right)
    {
        $result = new Matrix($left->getNbRows(), $left->getNbColumns(), $left->toArray());
        $result->multiply($right);
        return $result;
    }

    public static function subtract(Matrix $left, $right)
    {
        $result = new Matrix($left->getNbRows(), $left->getNbColumns(), $left->toArray());
        $result->subtract($right);
        return $result;
    }

    public static function createIdentity($rows, $columns)
    {
        $result = new Matrix($rows, $columns);
        $result->makeIdentity();
        return $result;
    }

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

    public static function createZero($rows, $columns)
    {
        $result = new Matrix($rows, $columns, array());
        $result->makeZero();
        return $result;
    }

    public static function transpose(Matrix $matrix)
    {
        $result = new Matrix($matrix->getNbRows(), $matrix->getNbColumns(), $matrix->toArray());
        $result->transpose();
        return $result;
    }
}
