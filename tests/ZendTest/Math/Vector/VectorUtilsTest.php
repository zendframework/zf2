<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math\Vector;

use Zend\Math\Vector\Vector;
use Zend\Math\Vector\VectorUtils;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @group      Zend_Math
 */
class VectorUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $vector1 = new Vector(array(1, 2));
        $vector2 = new Vector(array(2, 1));
        $vector3 = VectorUtils::add($vector1, $vector2);

        $this->assertEquals(2, $vector3->getDimension());
        $this->assertEquals(3, $vector3[0]);
        $this->assertEquals(3, $vector3[1]);
    }

    public function testSubtract()
    {
        $vector1 = new Vector(array(1, 2));
        $vector2 = new Vector(array(2, 1));
        $vector3 = VectorUtils::subtract($vector1, $vector2);

        $this->assertEquals(2, $vector3->getDimension());
        $this->assertEquals(-1, $vector3[0]);
        $this->assertEquals(1, $vector3[1]);
    }

    public function testCreateZero()
    {
        $vector = VectorUtils::createZero(3);

        $this->assertEquals(3, $vector->getDimension());
        $this->assertEquals(0, $vector[0]);
        $this->assertEquals(0, $vector[1]);
        $this->assertEquals(0, $vector[2]);
    }

    public function testCreateOne()
    {
        $vector = VectorUtils::createOne(3);

        $this->assertEquals(3, $vector->getDimension());
        $this->assertEquals(1, $vector[0]);
        $this->assertEquals(1, $vector[1]);
        $this->assertEquals(1, $vector[2]);
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

        $vector3 = VectorUtils::crossProduct($vector1, $vector2);

        $this->assertEquals(-3, $vector3[0]);
        $this->assertEquals(6, $vector3[1]);
        $this->assertEquals(-3, $vector3[2]);
    }

    public function testDistanceSameVector()
    {
        $vector = new Vector(array(1, 2, 3));

        $distance = VectorUtils::distance($vector, $vector);

        $this->assertEquals(0, $distance);
    }

    public function testDistanceSameValues()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(1, 2, 3));

        $distance = VectorUtils::distance($vector1, $vector2);

        $this->assertEquals(0, $distance);
    }

    public function testDistanceOtherValues()
    {
        $vector1 = new Vector(array(1, 2, 3));
        $vector2 = new Vector(array(2, 4, 6));

        $distance = VectorUtils::distance($vector1, $vector2);

        $this->assertEquals(3.7416573867739, $distance);
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

        $dotProduct = VectorUtils::dotProduct($vector1, $vector2);

        $this->assertEquals(32, $dotProduct);
    }
}
