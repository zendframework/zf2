<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class DbSelectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockSelect;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockStatement;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockResult;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockSql;

    /** @var DbSelect */
    protected $dbSelect;

    public function setup()
    {
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $this->mockResult = $mockResult;

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $this->mockStatement = $mockStatement;

        $this->mockStatement->expects($this->any())->method('execute')->will($this->returnValue($this->mockResult));

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));

        $mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $mockPlatform->expects($this->any())->method('getName')->will($this->returnValue('platform'));
        $mockAdapter = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Adapter',
            array($mockDriver, $mockPlatform)
        );

        $mockSql = $this->getMock(
            'Zend\Db\Sql\Sql',
            array('prepareStatementForSqlObject', 'execute'),
            array($mockAdapter)
        );
        $this->mockSql = $mockSql;
        $this->mockSql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($this->isInstanceOf('Zend\Db\Sql\Select'))
            ->will($this->returnValue($this->mockStatement));


        $this->mockSelect = $this->getMock('Zend\Db\Sql\Select');

        $this->dbSelect = new DbSelect($this->mockSelect, $mockSql);
    }

    public function testGetItems()
    {
        $this->mockSelect->expects($this->once())->method('limit')->with($this->equalTo(10));
        $this->mockSelect->expects($this->once())->method('offset')->with($this->equalTo(2));
        $items = $this->dbSelect->getItems(2, 10);
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $items);
    }

    public function testCount()
    {
        $this->mockSelect->expects($this->once())->method('columns')->with($this->equalTo(array('c' => new Expression('COUNT(1)'))));
-       $this->mockResult->expects($this->any())->method('current')->will($this->returnValue(array('c' => 5)));
-
-       $this->mockSelect->expects($this->exactly(6))->method('reset'); // called for columns, limit, offset, order
-       $this->mockSelect->expects($this->once())->method('getRawState')->with($this->equalTo(Select::JOINS))
-           ->will($this->returnValue(array(array('name' => 'Foo', 'on' => 'On Stuff', 'columns' => array('foo', 'bar'), 'type' => Select::JOIN_INNER))));
-       $this->mockSelect->expects($this->once())->method('join')->with(
-           'Foo',
-           'On Stuff',
-           array(),
-           Select::JOIN_INNER
-       );

        $count = $this->dbSelect->count();
        $this->assertEquals(5, $count);
    }
}
