<?php
namespace ZendTest\Db\TableGateway;

use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Sql,
    Zend\Db\ResultSet\ResultSet;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-03-01 at 21:02:22.
 */
class TableGatewayTest extends \PHPUnit_Framework_TestCase
{
    //protected $mockDriver = null;

    /**
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $mockAdapter = null;

    /**
     * @var TableGateway
     */
    protected $table;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(5));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));

        $mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $mockConnection->expects($this->any())->method('getLastGeneratedId')->will($this->returnValue(10));

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        $this->mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));


        $this->table = new TableGateway('foo', $this->mockAdapter);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getTableName
     */
    public function testGetTableName()
    {
        $this->assertEquals('foo', $this->table->getTable());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getAdapter
     */
    public function testGetAdapter()
    {
        $this->assertSame($this->mockAdapter, $this->table->getAdapter());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getDatabaseSchema
     */
    public function testGetDatabaseSchema()
    {
        $this->assertNull($this->table->getSchema());

        $table = new TableGateway('foo', $this->mockAdapter, 'FooSchema');
        $this->assertEquals('FooSchema', $table->getSchema());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::setSqlDelete
     */
    public function testSetSqlDelete()
    {
        $delete = new Sql\Delete;
        $this->table->setSqlDeletePrototype($delete);
        $this->assertSame($delete, $this->table->getSqlDeletePrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getSqlDelete
     */
    public function testGetSqlDelete()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Delete', $this->table->getSqlDeletePrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::setSqlInsert
     */
    public function testSetSqlInsert()
    {
        $insert = new Sql\Insert;
        $this->table->setSqlInsertPrototype($insert);
        $this->assertSame($insert, $this->table->getSqlInsertPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getSqlInsert
     */
    public function testGetSqlInsert()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Insert', $this->table->getSqlInsertPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::setSqlSelect
     */
    public function testSetSqlSelect()
    {
        $select = new Sql\Select;
        $this->table->setSqlSelectPrototype($select);
        $this->assertSame($select, $this->table->getSqlSelectPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getSqlSelect
     */
    public function testGetSqlSelect()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Select', $this->table->getSqlSelectPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::setSqlUpdate
     */
    public function testSetSqlUpdate()
    {
        $update = new Sql\Update;
        $this->table->setSqlUpdatePrototype($update);
        $this->assertSame($update, $this->table->getSqlUpdatePrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getSqlUpdate
     */
    public function testGetSqlUpdate()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Update', $this->table->getSqlUpdatePrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::setSelectResultPrototype
     */
    public function testSetSelectResultPrototype()
    {
        $resultSet = new ResultSet;
        $this->table->setSelectResultPrototype($resultSet);
        $this->assertSame($resultSet, $this->table->getSelectResultPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getSelectResultPrototype
     */
    public function testGetSelectResultPrototype()
    {
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $this->table->getSelectResultPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::select
     */
    public function testSelectWithNoWhere()
    {
        $select = $this->getMock('Zend\Db\Sql\Select');
        $select->expects($this->any())
            ->method('getRawState')
            ->will($this->returnValue(array(
                'table' => $this->table->getTable(),
                'schema' => ''
                ))
            );

        $this->table->setSqlSelectPrototype($select);
        $resultSet = $this->table->select();

        // check return types
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $resultSet);
        $this->assertNotSame($this->table->getSelectResultPrototype(), $resultSet);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::select
     */
    public function testSelectWithWhereString()
    {
        $select = $this->getMock('Zend\Db\Sql\Select');
        $select->expects($this->any())
            ->method('getRawState')
            ->will($this->returnValue(array(
                'table' => $this->table->getTable(),
                'schema' => ''
                ))
            );

        // assert select::from() is called
        $select->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $this->table->setSqlSelectPrototype($select);
        $this->table->select('foo');
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::insert
     */
    public function testInsert()
    {
        $insert = $this->getMock('Zend\Db\Sql\Insert');

        // assert ?
        $insert->expects($this->once())
            ->method('into')
            ->with($this->table->getTable());

        $insert->expects($this->once())
            ->method('prepareStatement')
            ->with($this->mockAdapter);


        $insert->expects($this->once())
            ->method('values')
            ->with($this->equalTo(array('foo' => 'bar')));

        $this->table->setSqlInsertPrototype($insert);

        $affectedRows = $this->table->insert(array('foo' => 'bar'));
        $this->assertEquals(5, $affectedRows);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::update
     */
    public function testUpdate()
    {
        $update = $this->getMock('Zend\Db\Sql\Update');

        // assert select::from() is called
        $update->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $this->table->setSqlUpdatePrototype($update);
        $this->table->update(array('foo' => 'bar'), 'foo');
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::delete
     */
    public function testDelete()
    {
        $delete = $this->getMock('Zend\Db\Sql\Delete');

        // assert select::from() is called
        $delete->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $this->table->setSqlDeletePrototype($delete);
        $this->table->delete('foo');
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getLastInsertId
     * @todo   Implement testGetLastInsertId().
     */
    public function testGetLastInsertId()
    {
        $this->table->insert(array('foo' => 'bar'));
        $this->assertEquals(10, $this->table->getLastInsertId());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__get
     * @todo   Implement test__get().
     */
    public function test__get()
    {
        $this->table->insert(array('foo')); // trigger last insert id update

        $this->assertEquals(10, $this->table->lastInsertId);
        $this->assertSame($this->mockAdapter, $this->table->adapter);
        $this->assertEquals('foo', $this->table->tableName);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__clone
     * @todo   Implement test__clone().
     */
    public function test__clone()
    {
        $cTable = clone $this->table;
        //$this->assertNotSame($this->mockAdapter, $cTable->getAdapter());
        $this->assertNotSame($this->table->getSqlInsertPrototype(), $cTable->getSqlInsertPrototype());
        $this->assertNotSame($this->table->getSqlUpdatePrototype(), $cTable->getSqlUpdatePrototype());
        $this->assertNotSame($this->table->getSqlDeletePrototype(), $cTable->getSqlDeletePrototype());
        $this->assertNotSame($this->table->getSqlSelectPrototype(), $cTable->getSqlSelectPrototype());
    }
}
