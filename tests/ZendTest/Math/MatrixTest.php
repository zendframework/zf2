<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math;

use Zend\Math\Matrix;

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

        $this->assertEquals(2, $matrix->getRowCount());
        $this->assertEquals(2, $matrix->getColumnCount());

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(0, $matrix[3]);
    }

    public function testConstructorIncompleteData()
    {
        $matrix = new Matrix(2, 2, array(1, 2));

        $this->assertEquals(2, $matrix->getRowCount());
        $this->assertEquals(2, $matrix->getColumnCount());

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(2, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(0, $matrix[3]);
    }

    public function testConstructorWithData()
    {
        $matrix = new Matrix(2, 2, array(1, 2, 3, 4));

        $this->assertEquals(2, $matrix->getRowCount());
        $this->assertEquals(2, $matrix->getColumnCount());

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

        $this->assertEquals(2, $matrix->getRowCount());
        $this->assertEquals(2, $matrix->getColumnCount());
    }

    public function testDivideByScalar()
    {
        $matrix = new Matrix(2, 2, array(2, 4, 6, 8));
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
        $matrix = new Matrix(2, 2, array(
            0, 1,
            2, 3,
        ));

        $this->assertEquals(0, $matrix[0]);
        $this->assertEquals(1, $matrix[1]);
        $this->assertEquals(2, $matrix[2]);
        $this->assertEquals(3, $matrix[3]);

        $matrix->makeIdentity();

        $this->assertEquals(1, $matrix[0]);
        $this->assertEquals(0, $matrix[1]);
        $this->assertEquals(0, $matrix[2]);
        $this->assertEquals(1, $matrix[3]);
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
