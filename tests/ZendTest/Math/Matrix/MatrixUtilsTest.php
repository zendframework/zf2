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
    }

    public function testMultiply()
    {
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
}
