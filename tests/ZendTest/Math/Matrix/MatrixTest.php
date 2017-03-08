<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math\Matrix;

use Zend\Math\Matrix\Matrix;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @group      Zend_Math
 */
class MatrixTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorNoData()
    {
        $matrix = new Matrix(2, 2);

        $this->assertEquals(2, $matrix->getNbRows());
        $this->assertEquals(2, $matrix->getNbColumns());

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(0, $matrix[3]);
    }

    public function testConstructorIncompleteData()
    {
        $matrix = new Matrix(2, 2, array(1, 2));

        $this->assertEquals(2, $matrix->getNbRows());
        $this->assertEquals(2, $matrix->getNbColumns());

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(0, $matrix[3]);
    }

    public function testConstructorWithData()
    {
        $matrix = new Matrix(2, 2, array(1, 2, 3, 4));

        $this->assertEquals(2, $matrix->getNbRows());
        $this->assertEquals(2, $matrix->getNbColumns());

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(3, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);
    }

    public function testAddScalarToZeroMatrix()
    {
        $matrix = new Matrix(2, 2);

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);

        $matrix->add(5);

        $this->assertEquals(5, $matrix[0]);
        $this->assertEquals(5, $matrix[1]);
    }

    public function testAddScalarToNonZeroMatrix()
    {
        $matrix = new Matrix(2, 2, array(1, 2, 3, 4));

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(3, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);

        $matrix->add(5);

        $this->assertEquals(6, $matrix[0]);
        $this->assertEquals(7, $matrix[1]);
        $this->assertEquals(8, $matrix[2]);
        $this->assertEquals(9, $matrix[3]);
    }

    public function testAddValidMatrixToMatrix()
    {
        $matrix1 = new Matrix(2, 2, array(1, 2, 3, 4));
        $matrix2 = new Matrix(2, 2, array(1, 2, 3, 4));
        $matrix2->add($matrix1);

        $this->assertEquals(2, $matrix2[0]);
        $this->assertEquals(4, $matrix2[1]);
        $this->assertEquals(6, $matrix2[2]);
        $this->assertEquals(8, $matrix2[3]);
    }

    public function testAddInvalidMatrixToMatrix()
    {
        $this->setExpectedException('InvalidArgumentException');

        $matrix1 = new Matrix(2, 2, array(1, 2, 3, 4));
        $matrix2 = new Matrix(2, 3);
        $matrix2->add($matrix1);
    }

    public function testCountable()
    {
        $matrix = new Matrix(2, 2);

        $this->assertEquals(4, $matrix->count());
        $this->assertEquals(4, count($matrix));
    }

    public function testCountRowsAndColumns()
    {
        $matrix = new Matrix(2, 2);

        $this->assertEquals(2, $matrix->getNbRows());
        $this->assertEquals(2, $matrix->getNbColumns());
    }

    public function testDeterminantNonSquare()
    {
        $this->setExpectedException('RuntimeException');

        $matrix = new Matrix(3, 5);
        $matrix->getDeterminant();
    }

    public function testDeterminant2x2()
    {
        $matrix = new Matrix(2, 2, array(1, 2, 3, 4));

        $this->assertEquals(-2, $matrix->getDeterminant());
    }

    public function testDeterminant3x3()
    {
        $matrix = new Matrix(3, 3, array(
            1,  2,  3,
            0, -4,  1,
            0,  3, -1
        ));

        $this->assertEquals(1, $matrix->getDeterminant());
    }

    public function testDeterminant4x4()
    {
        $matrix = new Matrix(4, 4, array(
            1,   1, 1, 1,
            29, 20, 2, 1,
            1,  20, 2, 1,
            1,  29, 1, 1,
        ));

        $this->assertEquals(784, $matrix->getDeterminant());
    }

    public function testDivideByScalar()
    {
        $matrix = new Matrix(3, 2, array(2, 4, 6, 8));
        $matrix->divide(2);

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(3, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);
    }

    public function testDivideByMatrix()
    {
        $this->setExpectedException('InvalidArgumentException');

        $matrix1 = new Matrix(2, 2, array(2, 4, 6, 8));

        $matrix2 = new Matrix(2, 2, array(2, 4, 6, 8));
        $matrix2->divide($matrix1);
    }

    public function testGetColumns()
    {
        $matrix = new Matrix(3, 3, array(
            1, 2, 3,
            4, 5, 6,
            7, 8, 9,
        ));

        $columns = $matrix->getColumns();
        $this->assertCount(3, $columns);

        $this->assertCount(3, $columns[0]);
        $this->assertCount(3, $columns[1]);
        $this->assertCount(3, $columns[2]);

        $this->assertEquals(1, $columns[0][0]);
        $this->assertEquals(4, $columns[0][1]);
        $this->assertEquals(7, $columns[0][2]);

        $this->assertEquals(2, $columns[1][0]);
        $this->assertEquals(5, $columns[1][1]);
        $this->assertEquals(8, $columns[1][2]);

        $this->assertEquals(3, $columns[2][0]);
        $this->assertEquals(6, $columns[2][1]);
        $this->assertEquals(9, $columns[2][2]);
    }

    public function testGetRows()
    {
        $matrix = new Matrix(3, 3, array(
            1, 2, 3,
            4, 5, 6,
            7, 8, 9,
        ));

        $rows = $matrix->getRows();
        $this->assertCount(3, $rows);

        $this->assertCount(3, $rows[0]);
        $this->assertCount(3, $rows[1]);
        $this->assertCount(3, $rows[2]);

        $this->assertEquals(1, $rows[0][0]);
        $this->assertEquals(2, $rows[0][1]);
        $this->assertEquals(3, $rows[0][2]);

        $this->assertEquals(4, $rows[1][0]);
        $this->assertEquals(5, $rows[1][1]);
        $this->assertEquals(6, $rows[1][2]);

        $this->assertEquals(7, $rows[2][0]);
        $this->assertEquals(8, $rows[2][1]);
        $this->assertEquals(9, $rows[2][2]);
    }

    public function testIsSquareFalse()
    {
        $matrix = new Matrix(2, 3);

        $this->assertFalse($matrix->isSquare());
    }

    public function testIsSquareTrue()
    {
        $matrix = new Matrix(2, 2);

        $this->assertTrue($matrix->isSquare());
    }

    public function testMakeIdentity()
    {
        $matrix = new Matrix(2, 2, array(0, 1, 2, 3));
        $matrix->makeIdentity();

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(1, $matrix[3]);
    }

    public function testMakeZero()
    {
        $matrix = new Matrix(2, 2, array(0, 1, 2, 3));
        $matrix->makeZero();

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(0, $matrix[3]);
    }

    public function testMultiplyByScalar()
    {
        $matrix = new Matrix(2, 2, array(
            4, 6,
            9, 1
        ));
        $matrix->multiply(5);

        $this->assertEquals(20, $matrix[0]);
        $this->assertEquals(30, $matrix[1]);
        $this->assertEquals(45, $matrix[2]);
        $this->assertEquals(5, $matrix[3]);
    }

    public function testMultiplyByMatrix()
    {
        $matrix1 = new Matrix(2, 2, array(4, 6, 9, 1));
        $matrix2 = new Matrix(2, 2, array(2, 3, 4, 5));

        $matrix1->multiply($matrix2);

        $this->assertEquals(32, $matrix1[0]);
        $this->assertEquals(42, $matrix1[1]);
        $this->assertEquals(22, $matrix1[2]);
        $this->assertEquals(32, $matrix1[3]);
    }

    public function testTranspose()
    {
        $matrix = new Matrix(2, 3, array(
            0, 1, 2,
            3, 4, 5,
        ));

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(1, $matrix[1]);
        $this->assertEquals(2, $matrix[2]);
        $this->assertEquals(3, $matrix[3]);
        $this->assertEquals(4, $matrix[4]);
        $this->assertEquals(5, $matrix[5]);

        $matrix->transpose();

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(3, $matrix[1]);
        $this->assertEquals(1, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);
        $this->assertEquals(2, $matrix[4]);
        $this->assertEquals(5, $matrix[5]);
    }

    public function testSubtractScalarFromZeroMatrix()
    {
        $matrix = new Matrix(2, 2);

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);

        $matrix->subtract(5);

        $this->assertEquals(-5, $matrix[0]);
        $this->assertEquals(-5, $matrix[1]);
    }

    public function testSubtractScalarFromNonZeroMatrix()
    {
        $matrix = new Matrix(2, 2, array(1, 2, 3, 4));

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(3, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);

        $matrix->subtract(5);

        $this->assertEquals(-4, $matrix[0]);
        $this->assertEquals(-3, $matrix[1]);
        $this->assertEquals(-2, $matrix[2]);
        $this->assertEquals(-1, $matrix[3]);
    }

    public function testSubtractValidMatrixFromMatrix()
    {
        $matrix1 = new Matrix(2, 2, array(1, 2, 3, 4));
        $matrix2 = new Matrix(2, 2, array(1, 2, 3, 4));
        $matrix2->subtract($matrix1);

        $this->assertEquals(0, $matrix2[0]);
        $this->assertEquals(0, $matrix2[1]);
        $this->assertEquals(0, $matrix2[2]);
        $this->assertEquals(0, $matrix2[3]);
    }

    public function testSubtractInvalidMatrixFromMatrix()
    {
        $this->setExpectedException('InvalidArgumentException');

        $matrix1 = new Matrix(2, 2, array(1, 2, 3, 4));
        $matrix2 = new Matrix(2, 3);
        $matrix2->subtract($matrix1);
    }

    public function testToString()
    {
        $matrix = new Matrix(2, 2, array(
            1, 2,
            3, 4
        ));

        $this->assertEquals('[[1,2][3,4]]', $matrix->toString());
    }

    public function testCastToString()
    {
        $matrix = new Matrix(2, 2, array(
            1, 2,
            3, 4
        ));

        $this->assertEquals('[[1,2][3,4]]', (string)$matrix);
    }

    public function testUnsetting()
    {
        $matrix = new Matrix(2, 2, array(
            1, 2,
            3, 4
        ));

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(3, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);

        unset($matrix[1]);

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);
        $this->assertEquals(3, $matrix[2]);
        $this->assertEquals(4, $matrix[3]);
    }
}
