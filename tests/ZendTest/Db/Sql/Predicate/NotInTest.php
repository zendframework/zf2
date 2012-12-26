<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\NotIn;

class NotInTest extends TestCase
{

    public function testEmptyConstructorYieldsNullIdentifierAndValueSet()
    {
        $notIn = new NotIn();
        $this->assertNull($notIn->getIdentifier());
        $this->assertNull($notIn->getValueSet());
    }

    public function testCanPassIdentifierAndValueSetToConstructor()
    {
        $notIn = new NotIn();
        $predicate = new NotIn('foo.bar', array(1, 2));
        $this->assertEquals('foo.bar', $predicate->getIdentifier());
        $this->assertEquals(array(1, 2), $predicate->getValueSet());
    }

    public function testIdentifierIsMutable()
    {
        $notIn = new NotIn();
        $notIn->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $notIn->getIdentifier());
    }

    public function testValueSetIsMutable()
    {
        $notIn = new NotIn();
        $notIn->setValueSet(array(1, 2));
        $this->assertEquals(array(1, 2), $notIn->getValueSet());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $notIn = new NotIn();
        $notIn->setIdentifier('foo.bar')
            ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            '%s NOT IN (%s, %s, %s)',
            array('foo.bar', 1, 2, 3),
            array(NotIn::TYPE_IDENTIFIER, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE, NotIn::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $notIn->getExpressionData());
    }

    public function testGetExpressionDataWithSubselect()
    {
        $notIn = new NotIn('foo', $select = new Select);
        $expected = array(array(
            '%s NOT IN %s',
            array('foo', $select),
            array($notIn::TYPE_IDENTIFIER, $notIn::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $notIn->getExpressionData());
    }
}
