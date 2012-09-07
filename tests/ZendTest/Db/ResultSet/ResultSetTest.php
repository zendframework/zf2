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

use ArrayObject;
use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;
use SplStack;
use stdClass;
use Zend\Db\ResultSet\ResultSet;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 */
class ResultSetTest extends TestCase
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

        $this->resultSet = new ResultSet;
    }

    public function testRowObjectPrototypeIsPopulatedByRowObjectByDefault()
    {
        $row = $this->resultSet->getArrayObjectPrototype();
        $this->assertInstanceOf('ArrayObject', $row);
    }

    public function testRowObjectPrototypeIsMutable()
    {
        $row = new \ArrayObject();
        $this->resultSet->setArrayObjectPrototype($row);
        $this->assertSame($row, $this->resultSet->getArrayObjectPrototype());
    }

    public function testRowObjectPrototypeMayBePassedToConstructor()
    {
        $row = new \ArrayObject();
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT, $row);
        $this->assertSame($row, $resultSet->getArrayObjectPrototype());
    }

    public function testReturnTypeIsObjectByDefault()
    {
        $this->assertEquals(ResultSet::TYPE_ARRAYOBJECT, $this->resultSet->getReturnType());
    }

    public function invalidReturnTypes()
    {
        return array(
            array(1),
            array(1.0),
            array(true),
            array('string'),
            array(array('foo')),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider invalidReturnTypes
     */
    public function testSettingInvalidReturnTypeRaisesException($type)
    {
        $this->setExpectedException('Zend\Db\ResultSet\Exception\InvalidArgumentException');
        new ResultSet(ResultSet::TYPE_ARRAYOBJECT, $type);
    }

    public function testDataSourceIsNullByDefault()
    {
        $this->assertNull($this->resultSet->getDataSource());
    }

    public function testCanProvideIteratorAsDataSource()
    {
        $it = new SplStack;
        $this->resultSet->initialize($it);
        $this->assertSame($it, $this->resultSet->getDataSource());
    }

    public function testCanProvideIteratorAggregateAsDataSource()
    {
        $iteratorAggregate = $this->getMock('IteratorAggregate', array('getIterator'), array(new SplStack));
        $iteratorAggregate->expects($this->any())->method('getIterator')->will($this->returnValue($iteratorAggregate));
        $this->resultSet->initialize($iteratorAggregate);
        $this->assertSame($iteratorAggregate->getIterator(), $this->resultSet->getDataSource());
    }

    /**
     * @dataProvider invalidReturnTypes
     */
    public function testInvalidDataSourceRaisesException($dataSource)
    {
        if (is_array($dataSource)) {
            // this is valid
            return;
        }
        $this->setExpectedException('Zend\Db\ResultSet\Exception\InvalidArgumentException');
        $this->resultSet->initialize($dataSource);
    }

    public function testFieldCountIsZeroWithNoDataSourcePresent()
    {
        $this->assertEquals(0, $this->resultSet->getFieldCount());
    }

    public function getArrayDataSource($count)
    {
        $array = array();
        for ($i = 0; $i < $count; $i++) {
            $array[] = array(
                'id'    => $i,
                'title' => 'title ' . $i,
            );
        }
        return new ArrayIterator($array);
    }

    public function testFieldCountRepresentsNumberOfFieldsInARowOfData()
    {
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $dataSource = $this->getArrayDataSource(10);
        $resultSet->initialize($dataSource);
        $this->assertEquals(2, $resultSet->getFieldCount());
    }

    public function testWhenReturnTypeIsArrayThenIterationReturnsArrays()
    {
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $dataSource = $this->getArrayDataSource(10);
        $resultSet->initialize($dataSource);
        foreach ($resultSet as $index => $row) {
            $this->assertEquals($dataSource[$index], $row);
        }
    }

    public function testWhenReturnTypeIsObjectThenIterationReturnsRowObjects()
    {
        $dataSource = $this->getArrayDataSource(10);
        $this->resultSet->initialize($dataSource);
        foreach ($this->resultSet as $index => $row) {
            $this->assertInstanceOf('ArrayObject', $row);
            $this->assertEquals($dataSource[$index], $row->getArrayCopy());
        }
    }

    public function testCountReturnsCountOfRows()
    {
        $count      = rand(3, 75);
        $dataSource = $this->getArrayDataSource($count);
        $this->resultSet->initialize($dataSource);
        $this->assertEquals($count, $this->resultSet->count());
    }

    public function testToArrayRaisesExceptionForRowsThatAreNotArraysOrArrayCastable()
    {
        $count      = rand(3, 75);
        $dataSource = $this->getArrayDataSource($count);
        foreach ($dataSource as $index => $row) {
            $dataSource[$index] = (object) $row;
        }
        $this->resultSet->initialize($dataSource);
        $this->setExpectedException('Zend\Db\ResultSet\Exception\RuntimeException');
        $this->resultSet->toArray();
    }

    public function testToArrayCreatesArrayOfArraysRepresentingRows()
    {
        $count      = rand(3, 75);
        $dataSource = $this->getArrayDataSource($count);
        $this->resultSet->initialize($dataSource);
        $test = $this->resultSet->toArray();
        $this->assertEquals($dataSource->getArrayCopy(), $test, var_export($test, 1));
    }

    public function testBufferingCallsDatasourceCurrentOnce()
    {
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockResult->expects($this->once())->method('current')->will($this->returnValue(array('foo' => 'bar')));

        $this->resultSet->initialize($mockResult);
        $this->resultSet->buffer();
        $this->resultSet->current();

        // assertion above will fail if this calls datasource current
        $this->resultSet->current();
    }

    public function testCallingBufferAfterIterationThrowsException()
    {
        $this->resultSet->initialize($this->getMock('Zend\Db\Adapter\Driver\ResultInterface'));
        $this->resultSet->current();

        $this->setExpectedException('Zend\Db\ResultSet\Exception\RuntimeException', 'Buffering must be enabled before iteration is started');
        $this->resultSet->buffer();
    }

    /**
     * @group simpleArray
     */
    public function testDataSourceSimpleArray()
    {
        $dataSource = array(0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six');
        $this->resultSet->initialize($dataSource);

        $this->resultSet->next();
        $this->resultSet->next();

        // 2 times next
        $this->assertSame(2, $this->resultSet->key());
        $this->assertSame('two', $this->resultSet->current());

        $this->resultSet->next();
        $this->resultSet->next();
        // 2 times next again
        $this->assertSame(4, $this->resultSet->key());
        $this->assertSame('four', $this->resultSet->current());

        $this->resultSet->next();
        $this->resultSet->rewind();
        // resultset rewinded, so key should be 0 again
        $this->assertSame(0, $this->resultSet->key());
        $this->assertSame('zero', $this->resultSet->current());

        $this->resultSet->next();
        /// 1 times next so the key should be 1
        $this->assertSame(1, $this->resultSet->key());
        $this->assertSame('one', $this->resultSet->current());
    }

    /**
     * @group arrayObject
     */
    public function testDataSourceArrayObject()
    {
        $dataSource = $this->getArrayDataSource(10);
        $this->resultSet->initialize($dataSource);

        $this->resultSet->next();
        $this->resultSet->next();

        // 2 times next
        $this->assertSame(2, $this->resultSet->key());
        $this->assertInstanceOf('ArrayObject', $this->resultSet->current());
        $this->assertSame('title 2', $this->resultSet->current()->title);

        $this->resultSet->next();
        $this->resultSet->next();
        // 2 times next again
        $this->assertSame(4, $this->resultSet->key());
        $this->assertSame('title 4', $this->resultSet->current()->title);

        $this->resultSet->next();
        $this->resultSet->rewind();
        // resultset rewinded, so key should be 0 again
        $this->assertSame(0, $this->resultSet->key());
        $this->assertSame('title 0', $this->resultSet->current()->title);

        $this->resultSet->next();
        /// 1 times next so the key should be 1
        $this->assertSame(1, $this->resultSet->key());
        $this->assertSame('title 1', $this->resultSet->current()->title);
    }

    /**
     * @group ResultInterfacetest
     */
    public function testDataSourceResultInterface()
    {
        $this->markTestIncomplete(
            'Still have to create a good test for ResultInterface'
        );

    }
}
