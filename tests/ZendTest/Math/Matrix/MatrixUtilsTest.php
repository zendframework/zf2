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
use Zend\Math\Matrix\MatrixUtils;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @group      Zend_Math
 */
class MatrixUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testAddScalarToMatrix()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = MatrixUtils::add($matrix1, 5);

        $this->assertEquals(7, $matrix2[0]);
        $this->assertEquals(7, $matrix2[1]);
        $this->assertEquals(7, $matrix2[2]);
        $this->assertEquals(7, $matrix2[3]);
    }

    public function testAddTwoMatrices()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix3 = MatrixUtils::add($matrix1, $matrix2);

        $this->assertEquals(4, $matrix3[0]);
        $this->assertEquals(4, $matrix3[1]);
        $this->assertEquals(4, $matrix3[2]);
        $this->assertEquals(4, $matrix3[3]);
    }

    public function testDivide()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = MatrixUtils::divide($matrix1, 2);

        $this->assertEquals(1, $matrix2[0]);
        $this->assertEquals(1, $matrix2[1]);
        $this->assertEquals(1, $matrix2[2]);
        $this->assertEquals(1, $matrix2[3]);
    }

    public function testMultiplyByScalar()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = MatrixUtils::multiply($matrix1, 3);

        $this->assertEquals(6, $matrix2[0]);
        $this->assertEquals(6, $matrix2[1]);
        $this->assertEquals(6, $matrix2[2]);
        $this->assertEquals(6, $matrix2[3]);
    }

    public function testMultiplyByMatrix()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix3 = MatrixUtils::multiply($matrix1, $matrix2);

        $this->assertEquals(8, $matrix3[0]);
        $this->assertEquals(8, $matrix3[1]);
        $this->assertEquals(8, $matrix3[2]);
        $this->assertEquals(8, $matrix3[3]);
    }

    public function testSubtractScalarFromMatrix()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = MatrixUtils::subtract($matrix1, 5);

        $this->assertEquals(-3, $matrix2[0]);
        $this->assertEquals(-3, $matrix2[1]);
        $this->assertEquals(-3, $matrix2[2]);
        $this->assertEquals(-3, $matrix2[3]);
    }

    public function testSubtractTwoMatrices()
    {
        $matrix1 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix2 = new Matrix(2, 2, array(2, 2, 2, 2));
        $matrix3 = MatrixUtils::subtract($matrix1, $matrix2);

        $this->assertEquals(0, $matrix3[0]);
        $this->assertEquals(0, $matrix3[1]);
        $this->assertEquals(0, $matrix3[2]);
        $this->assertEquals(0, $matrix3[3]);
    }

    public function testCreateSubmatrix()
    {
        $matrix1 = new Matrix(3, 3, array(
            1, 2, 3,
            4, 5, 6,
            7, 8, 9,
        ));
        $matrix2 = MatrixUtils::createSubmatrix($matrix1, 1, 2);

        $this->assertEquals(2, $matrix2->getNbRows());
        $this->assertEquals(2, $matrix2->getNbColumns());

        $this->assertEquals(1, $matrix2[0]);
        $this->assertEquals(2, $matrix2[1]);
        $this->assertEquals(7, $matrix2[2]);
        $this->assertEquals(8, $matrix2[3]);
    }

    public function testTranspose()
    {
        $old = new Matrix(2, 2, array(
            1, 2,
            3, 4,
        ));
        $new = MatrixUtils::transpose($old);

        // The old matrix should have stayed the same:
        $this->assertEquals(1, $old[0]);
        $this->assertEquals(2, $old[1]);
        $this->assertEquals(3, $old[2]);
        $this->assertEquals(4, $old[3]);

        // And the new matrix should be transposed:
        $this->assertEquals(1, $new[0]);
        $this->assertEquals(3, $new[1]);
        $this->assertEquals(2, $new[2]);
        $this->assertEquals(4, $new[3]);
    }
}
