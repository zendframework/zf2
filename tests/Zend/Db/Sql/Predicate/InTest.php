<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Db\Sql\Predicate\In;

class InTest extends TestCase
{

    public function testEmptyConstructorYieldsNullIdentifierAndValueSet()
    {
        $in = new In();
        $this->assertNull($in->getIdentifier());
        $this->assertNull($in->getValueSet());
    }

    public function testSpecificationHasSaneDefaultValue()
    {
        $in = new In();
        $this->assertEquals('%1$s IN (%2$s)', $in->getSpecification());
    }

    public function testCanPassIdentifierAndValueSetToConstructor()
    {
        $in = new In();
        $predicate = new In('foo.bar', array(1, 2));
        $this->assertEquals('foo.bar', $predicate->getIdentifier());
        $this->assertEquals(array(1, 2), $predicate->getValueSet());
    }

    public function testIdentifierIsMutable()
    {
        $in = new In();
        $in->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $in->getIdentifier());
    }

    public function testValueSetIsMutable()
    {
        $in = new In();
        $in->setValueSet(array(1, 2));
        $this->assertEquals(array(1, 2), $in->getValueSet());
    }

    public function testSpecificationIsMutable()
    {
        $in = new In();
        $in->setSpecification('%1$s IS IN (%2$s)');
        $this->assertEquals('%1$s IS IN (%2$s)', $in->getSpecification());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $in = new In();
        $in->setIdentifier('foo.bar')
                        ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            $in->getSpecification(),
            array('foo.bar', 1, 2, 3),
            array(In::TYPE_IDENTIFIER, In::TYPE_VALUE, In::TYPE_VALUE, In::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }
}
