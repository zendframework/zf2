<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Pdo;

use ZendTest\Db\TestAsset\StubConnection;

class ConnectionTransactionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var stubConnection
     */
    protected $stubConnection = null;

    protected function setUp()
    {
        $this->stubConnection = new StubConnection();
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     */
    public function testBeginTransactionReturnsInstanceOfConnection()
    {
        $this->assertInstanceOf('\Zend\Db\Adapter\Driver\Pdo\Connection', $this->stubConnection->beginTransaction());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testBeginTransactionSetsInTransactionAtTrue()
    {
        $this->stubConnection->beginTransaction();
        $this->assertTrue($this->stubConnection->inTransaction());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testCommitReturnsInstanceOfConnection()
    {
        $this->stubConnection->beginTransaction();
        $this->assertInstanceOf('\Zend\Db\Adapter\Driver\Pdo\Connection', $this->stubConnection->commit());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testCommitSetsInTransactionAtFalse()
    {
        $this->stubConnection->beginTransaction();
        $this->stubConnection->commit();
        $this->assertFalse($this->stubConnection->inTransaction());
    }

    /**
     * Standalone commit after a SET autocommit=0;
     *
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testCommitWithoutBeginReturnsInstanceOfConnection()
    {
        $this->assertInstanceOf('\Zend\Db\Adapter\Driver\Pdo\Connection', $this->stubConnection->commit());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testNestedTransactionsCommit()
    {
        $nested = 0;

        $this->assertFalse($this->stubConnection->inTransaction());

        // 1st transaction
        $this->stubConnection->beginTransaction();
        $this->assertTrue($this->stubConnection->inTransaction());
        $this->assertSame(++ $nested, $this->stubConnection->getNestedTransactionsCount());

        // 2nd transaction
        $this->stubConnection->beginTransaction();
        $this->assertTrue($this->stubConnection->inTransaction());
        $this->assertSame(++ $nested, $this->stubConnection->getNestedTransactionsCount());

        // 1st commit
        $this->stubConnection->commit();
        $this->assertTrue($this->stubConnection->inTransaction());
        $this->assertSame(-- $nested, $this->stubConnection->getNestedTransactionsCount());

        // 2nd commit
        $this->stubConnection->commit();
        $this->assertFalse($this->stubConnection->inTransaction());
        $this->assertSame(-- $nested, $this->stubConnection->getNestedTransactionsCount());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::beginTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testNestedTransactionsRollback()
    {
        $nested = 0;

        $this->assertFalse($this->stubConnection->inTransaction());

        // 1st transaction
        $this->stubConnection->beginTransaction();
        $this->assertTrue($this->stubConnection->inTransaction());
        $this->assertSame(++ $nested, $this->stubConnection->getNestedTransactionsCount());

        // 2nd transaction
        $this->stubConnection->beginTransaction();
        $this->assertTrue($this->stubConnection->inTransaction());
        $this->assertSame(++ $nested, $this->stubConnection->getNestedTransactionsCount());

        // Rollback
        $this->stubConnection->rollback();
        $this->assertFalse($this->stubConnection->inTransaction());
        $this->assertSame(0, $this->stubConnection->getNestedTransactionsCount());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @expectedException \Zend\Db\Adapter\Exception\RuntimeException
     * @expectedExceptionMessage Must be connected before you can rollback
     */
    public function testRollbackDisconnectedThrowsException()
    {
        $this->stubConnection->disconnect();
        $this->stubConnection->rollback();
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     */
    public function testRollbackReturnsInstanceOfConnection()
    {
        $this->stubConnection->beginTransaction();
        $this->assertInstanceOf('\Zend\Db\Adapter\Driver\Pdo\Connection', $this->stubConnection->rollback());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     */
    public function testRollbackSetsInTransactionAtFalse()
    {
        $this->stubConnection->beginTransaction();
        $this->stubConnection->rollback();
        $this->assertFalse($this->stubConnection->inTransaction());
    }

    /**
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::rollback()
     * @expectedException \Zend\Db\Adapter\Exception\RuntimeException
     * @expectedExceptionMessage Must call beginTransaction() before you can rollback
     */
    public function testRollbackWithoutBeginThrowsException()
    {
        $this->stubConnection->rollback();
    }

    /**
     * Standalone commit after a SET autocommit=0;
     *
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::inTransaction()
     * @covers \Zend\Db\Adapter\Driver\Pdo\Connection::commit()
     */
    public function testStandaloneCommit()
    {
        $this->assertFalse($this->stubConnection->inTransaction());
        $this->assertSame(0, $this->stubConnection->getNestedTransactionsCount());

        $this->stubConnection->commit();

        $this->assertFalse($this->stubConnection->inTransaction());
        $this->assertSame(0, $this->stubConnection->getNestedTransactionsCount());
    }
}
