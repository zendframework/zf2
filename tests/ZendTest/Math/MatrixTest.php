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
