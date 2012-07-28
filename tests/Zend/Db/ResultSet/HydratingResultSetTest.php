<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace ZendTest\Db\ResultSet;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Stdlib\Hydrator\ArraySerializable;
use ArrayObject;
use StdClass;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 */
class HydratingResultSetTest extends TestCase
{
    /**
     * @var ResultSet
     */
    protected $resultSet;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->resultSet = new HydratingResultSet;
    }

    public function testRowObjectPrototypeIsArrayObjectAndHydratorIsArraySerializableByDefault()
    {
        $row = $this->resultSet->getObjectPrototype();
        $this->assertInstanceOf('ArrayObject', $row);

        $hydrator = $this->resultSet->getHydrator();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\ArraySerializable', $hydrator);
    }

    public function testRowObjectPrototypeIsMutable()
    {
        $row = new StdClass();
        $this->resultSet->setObjectPrototype($row);
        $this->assertSame($row, $this->resultSet->getObjectPrototype());
    }

    public function testHydratorAndRowObjectPrototypeMayBePassedToConstructor()
    {
        $row = new StdClass();
        $hydrator = new ObjectProperty;
        $resultSet = new HydratingResultSet($hydrator, $row);
        $this->assertSame($row, $resultSet->getObjectPrototype());
        $this->assertSame($hydrator, $resultSet->getHydrator());
    }

    public function getData()
    {
        return array(
            array(
                'id' => 1,
                'title' => 'hello',
            )
        );
    }
    public function testObjectPrototypeIsHydratedByHydrator()
    {
        $resultSet = new HydratingResultSet(new ObjectProperty, new StdClass);
        $resultSet->initialize($this->getData());
        $row = $resultSet->current();
        $this->assertEquals(1, $row->id);
        $this->assertEquals('hello', $row->title);
    }

    public function testToArrayReturnsArrayOfArraysByDefault()
    {
        $resultSet = new HydratingResultSet(new ObjectProperty, new StdClass);
        $resultSet->initialize($this->getData());
        $array = $resultSet->toArray();
        $this->assertEquals($this->getData(), $array);
    }

    public function testToArrayReturnsArrayOfHydratedObjects()
    {
        $resultSet = new HydratingResultSet(new ObjectProperty, new StdClass);
        $resultSet->initialize($this->getData());
        $array = $resultSet->toArray(HydratingResultSet::ARRAY_RAW);
        $this->assertInstanceOf('StdClass', $array[0]);
    }
}
