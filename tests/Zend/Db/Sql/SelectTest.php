<?php
namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Select,
    Zend\Db\Sql\Where;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Select
     */
    protected $select;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->select = new Select;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Zend\Db\Sql\Select::from
     */
    public function testFrom()
    {
        $this->select->from('foo', 'bar');
        $this->assertEquals('foo', $this->readAttribute($this->select, 'table'));
        $this->assertEquals('bar', $this->readAttribute($this->select, 'databaseOrSchema'));
    }

    /**
     * @covers Zend\Db\Sql\Select::columns
     */
    public function testColumns()
    {
        $this->select->columns(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->readAttribute($this->select, 'columns'));
    }

    /**
     * @covers Zend\Db\Sql\Select::join
     */
    public function testJoin()
    {
        $this->select->join('foo', 'x = y', Select::SQL_WILDCARD, Select::JOIN_INNER);
        $this->assertEquals(
            array(array('foo', 'x = y', array(Select::SQL_WILDCARD), Select::JOIN_INNER)),
            $this->readAttribute($this->select, 'joins')
        );
    }

    /**
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhere()
    {
        $this->select->where('x = y');
        $this->select->where(array('foo > ?' => 5));
        $this->select->where(array('id' => 2));
        $this->select->where(array('a = b'), Where::OP_OR);
        $where = $this->select->where;

        $predicates = $this->readAttribute($where, 'predicates');
        $this->assertEquals('AND', $predicates[0][0]);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Literal', $predicates[0][1]);

        $this->assertEquals('AND', $predicates[1][0]);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Literal', $predicates[1][1]);

        $this->assertEquals('AND', $predicates[2][0]);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Operator', $predicates[2][1]);

        $this->assertEquals('OR', $predicates[3][0]);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Literal', $predicates[3][1]);

        $where = new Where;
        $this->select->where($where);
        $this->assertSame($where, $this->select->where);

        $test = $this;
        $this->select->where(function ($what) use ($test, $where) {
            $test->assertSame($where, $what);
        });
    }

    /**
     * @covers Zend\Db\Sql\Select::prepareStatement
     */
    public function testPrepareStatement()
    {
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->at(0))
            ->method('setSql')
            ->with($this->equalTo('SELECT "bee", "baz", "zac".* FROM "foo" INNER JOIN "zac" ON "m" = "n"'));
        $mockStatement->expects($this->at(3))
            ->method('setSql')
            ->with($this->equalTo(' WHERE x = y'));

        $this->select->from('foo')
            ->columns(array('bee', 'baz'))
            ->join('zac', 'm = n')
            ->where('x = y');

        $this->select->prepareStatement($mockAdapter, $mockStatement);
    }

    /**
     * @covers Zend\Db\Sql\Select::getSqlString
     */
    public function testGetSqlString()
    {
        $this->select->from('foo')
            ->columns(array('bee', 'baz'))
            ->join('zac', 'm = n')
            ->where('x = y');
        $this->assertEquals('SELECT "bee", "baz", "zac".* FROM "foo" INNER JOIN "zac" ON "m" = "n" WHERE x = y', $this->select->getSqlString());
    }

    /**
     * @covers Zend\Db\Sql\Select::__get
     */
    public function test__get()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Where', $this->select->where);
    }

    /**
     * @covers Zend\Db\Sql\Select::__clone
     */
    public function test__clone()
    {
        $select1 = clone $this->select;
        $select1->where('id = foo');

        $this->assertEquals(0, $this->select->where->count());
        $this->assertEquals(1, $select1->where->count());
    }
}
