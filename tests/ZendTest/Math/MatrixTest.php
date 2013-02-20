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

use Zend\Math\Vector;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @group      Zend_Math
 */
class VectorTest extends \PHPUnit_Framework_TestCase
{
    public function testAdditionScalar()
    {
        $vector = new Vector(array(1, 2, 3));
        $vector->add(5);

        $this->assertEquals(3, count($vector));
        $this->assertEquals(6, $vector[0]);
        $this->assertEquals(7, $vector[1]);
        $this->assertEquals(8, $vector[2]);
    }

    public function testAdditionVector()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(4, 5, 6));

        $vector1->add($vector2);

        $this->assertEquals(3, count($vector1));
        $this->assertEquals(3, count($vector2));
        $this->assertEquals(5, $vector1[0]);
        $this->assertEquals(7, $vector1[1]);
        $this->assertEquals(9, $vector1[2]);
    }

    public function testCountable()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(1, 2, 3, 4, 5));

        $this->assertEquals(3, count($vector1));
        $this->assertEquals(5, count($vector2));
    }

    public function testCountMethod()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(1, 2, 3, 4, 5));

        $this->assertEquals(3, $vector1->count());
        $this->assertEquals(5, $vector2->count());
    }

    public function testCrossProduct()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(4, 5, 6));

        $this->assertEquals(1, $vector1[0]);
        $this->assertEquals(2, $vector1[1]);
        $this->assertEquals(3, $vector1[2]);

        $this->assertEquals(4, $vector2[0]);
        $this->assertEquals(5, $vector2[1]);
        $this->assertEquals(6, $vector2[2]);

        $vector3 = Vector::crossProduct($vector1, $vector2);

        $this->assertEquals(-3, $vector3[0]);
        $this->assertEquals(6, $vector3[1]);
        $this->assertEquals(-3, $vector3[2]);
    }

    public function testDivide()
    {
        $vector = new Vector(array(1, -2, 3));

        $this->assertEquals(1, $vector[0]);
        $this->assertEquals(-2, $vector[1]);
        $this->assertEquals(3, $vector[2]);

        $vector->divide(2);

        $this->assertEquals(0.5, $vector[0]);
        $this->assertEquals(-1, $vector[1]);
        $this->assertEquals(1.5, $vector[2]);
    }

    public function testDotProduct()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(4, 5, 6));

        $this->assertEquals(1, $vector1[0]);
        $this->assertEquals(2, $vector1[1]);
        $this->assertEquals(3, $vector1[2]);

        $this->assertEquals(4, $vector2[0]);
        $this->assertEquals(5, $vector2[1]);
        $this->assertEquals(6, $vector2[2]);

        $dotProduct = Vector::dotProduct($vector1, $vector2);

        $this->assertEquals(32, $dotProduct);
    }

    public function testGetDistance()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(2, 4, 6));

        $this->assertEquals(3.7416573867739, $vector1->getDistance($vector2));
    }

    public function testGetDistanceZero()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(1, 2, 3));

        $this->assertEquals(0, $vector1->getDistance($vector2));
    }

    public function testGetLength()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals(3.7416573867739, $vector->getLength());
    }

    public function testGetMagnitude()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals(3.7416573867739, $vector->getMagnitude());
    }

    public function testGetSquaredLength()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals(14, $vector->getSquaredLength());
    }

    public function testMultiply()
    {
        $vector = new Vector(array(1, -2, 3));

        $this->assertEquals(1, $vector[0]);
        $this->assertEquals(-2, $vector[1]);
        $this->assertEquals(3, $vector[2]);

        $vector->multiply(10);

        $this->assertEquals(10, $vector[0]);
        $this->assertEquals(-20, $vector[1]);
        $this->assertEquals(30, $vector[2]);
    }

    public function testNegate()
    {
        $vector = new Vector(array(1, -2, 3));

        $this->assertEquals(1, $vector[0]);
        $this->assertEquals(-2, $vector[1]);
        $this->assertEquals(3, $vector[2]);

        $vector->negate();

        $this->assertEquals(-1, $vector[0]);
        $this->assertEquals(2, $vector[1]);
        $this->assertEquals(-3, $vector[2]);
    }

    public function testNormalize()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals(1, $vector[0]);
        $this->assertEquals(2, $vector[1]);
        $this->assertEquals(3, $vector[2]);

        $vector->normalize();

        $this->assertEquals(0.26726124191242, $vector[0]);
        $this->assertEquals(0.53452248382485, $vector[1]);
        $this->assertEquals(0.80178372573727, $vector[2]);
    }

    public function testSubtractScalar()
    {
        $vector = new Vector(array(1, 2, 3));
        $vector->subtract(5);

        $this->assertEquals(3, count($vector));
        $this->assertEquals(-4, $vector[0]);
        $this->assertEquals(-3, $vector[1]);
        $this->assertEquals(-2, $vector[2]);
    }

    public function testSubtractVector()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(4, 5, 6));

        $vector1->subtract($vector2);

        $this->assertEquals(3, count($vector1));
        $this->assertEquals(3, count($vector2));
        $this->assertEquals(-3, $vector1[0]);
        $this->assertEquals(-3, $vector1[1]);
        $this->assertEquals(-3, $vector1[2]);
    }

    public function testToString()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals('[1,2,3]', $vector->toString());
    }

    public function testCastToString()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals('[1,2,3]', (string)$vector);
    }

    public function testUnsetting()
    {
        $vector = new Vector(array(1, 2, 3));

        $this->assertEquals(1, $vector[0]);
        $this->assertEquals(2, $vector[1]);
        $this->assertEquals(3, $vector[2]);

        unset($vector[0]);

        $this->assertEquals(2, $vector[0]);
        $this->assertEquals(3, $vector[1]);
    }
}
