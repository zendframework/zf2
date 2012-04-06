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
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Session\SaveHandler;

use Zend\Session\SaveHandler\TableGateway,
    Zend\Session\Exception as SaveHandlerException,
    Zend\Session\Manager,
    Zend\Debug,
    Zend\Db\TableGateway\TableGateway as BaseTableGateway,
    Zend\Db\Sql,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Db,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AbstractAdapter,
    Zend\Config\Config as Configuration,
    Zend\Session\SessionManager;


/**
 * Unit testing for DbTable include all tests for
 * regular session handling
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Session
 * @group      Zend_Session_SaveHandler
 * @group      Zend_Db_TableGateway
 */
class TableGatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * These options should match the default settings in
     * @see Zend\Session\SaveHandler\TableGateway
     *
     * @var array $saveHandlerOptions
     */
    protected $saveHandlerOptions = array(
        'tableName'       => 'sessions',
        'primary'         => 'sessions_id',
        'dataColumn'      => 'data',
        'timestampColumn' => 'timestamp',
        'lifetime'        => 0,
    );

    /**
     * @var Zend\Config\Config|Configuration $saveHandlerConfiguration
     */
    protected $saveHandlerConfiguration;

    /**
     * @var Zend\Db\Adapter\Adapter
     */
    protected $db;

    /**
     * @var Zend\Db\TableGateway\TableGateway
     */
    protected $table;

    /**
     * @var Zend\Session\SessionManager
     */
    protected $manager;

    /**
     * Array to collect used DbTable objects, so they are not
     * destroyed before all tests are done and session is not closed
     *
     * @var array
     */
    protected $_usedSaveHandlers = array();

    /**
     * Setup performed prior to each test method
     *
     * @return void
     */
    public function setUp()
    {
    }

    /**
     * Sets up the database connection and creates the table for session data
     *
     * @uses TableGatewayTest::setupTable()
     *
     * @param  Zend\Config\Config $primary
     * @return void
     */
    protected function setupDb()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension must be available and enabled for this test');
        }

        $this->dropTable();

        $query = 'CREATE TABLE IF NOT EXISTS `sessions` (
          `sessions_id` varbinary(32) NOT NULL,
          `timestamp` int(10) NOT NULL,
          `data` longtext NOT NULL,
          PRIMARY KEY (`sessions_id`)
        );';

        $options = array('driver' => 'Pdo_Sqlite', 'database' => 'savehandler_test.db',);
        $this->db = new \Zend\Db\Adapter\Adapter($options);

        //$this->db->query('ALTER TABLE ADD INDEX(`foo_index`) ON (`foo_column`))', Adapter::QUERY_MODE_EXECUTE);

        $this->db->query($query, Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * Sets up the database connection and creates the table for session data
     *
     * @param  Zend\Config\Config $primary
     * @return void
     */
    protected function setupTable()
    {
        $this->table = new BaseTableGateway($this->saveHandlerOptions['tableName'], $this->db);

        //Debug::dump($this->table, eval('return __FILE__;'));
    }

    /**
     * Drops the database table for session data
     *
     * @return void
     */
    protected function dropTable()
    {
        if (!$this->db instanceof \Zend\Db\Adapter\Adapter) {
            return;
        }

        $this->db->query('DROP TABLE IF EXISTS sessions', Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * Tear-down operations performed after each test method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->dropTable();
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::getAdapter
     */
    public function testAdapterIsProperlyInstantiated()
    {
        $this->setupDb();
        $this->assertTrue($this->db instanceof \Zend\Db\Adapter\Adapter);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::insert
     * @covers Zend\Db\TableGateway\TableGateway::select
     */
    public function testInsertTwoRowsAndPullFirstRow()
    {
        $this->setupDb();
        $this->setupTable();

        $data1 = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => '3',
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data1);

        $data2 = array(
            $this->saveHandlerOptions['primary'] => md5(2),
            $this->saveHandlerOptions['timestampColumn']   => '30',
            $this->saveHandlerOptions['dataColumn']        => '40',
        );
        $this->table->insert($data2);

        $id = md5(1);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception('Could not find row:' . $id);
        }

        //Debug::dump((array) $row, eval('return __FILE__;'));

        $this->assertSame($data1, (array) $row);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::insert
     * @covers Zend\Db\TableGateway\TableGateway::update
     * @covers Zend\Db\TableGateway\TableGateway::select
     */
    public function testInsertAndUpdateRows()
    {
        $this->setupDb();
        $this->setupTable();

        $id1 = md5(1);
        $id2 = md5(2);

        $data1 = array(
            $this->saveHandlerOptions['primary'] => $id1,
            $this->saveHandlerOptions['timestampColumn']   => '3',
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data1);

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id1));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();

        $message = 'Could not find row:' . $id1;
        $this->assertNotEmpty($row, $message);

        //Debug::dump((array) $row, eval('return __FILE__;'));

        $this->assertSame($data1, (array) $row);

        $data2 = array(
            $this->saveHandlerOptions['primary']     => $id2,
            $this->saveHandlerOptions['timestampColumn']     => '30',
            $this->saveHandlerOptions['dataColumn']             => '40',
        );
        $this->table->update($data2, array($this->saveHandlerOptions['primary'] => $id1));

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id2));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();

        $message = 'Could not find row:' . $id2;
        $this->assertNotEmpty($row, $message);

        //Debug::dump((array) $row, eval('return __FILE__;'));

        $this->assertSame($data2, (array) $row);

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id1));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();

        $message = 'Found original row:' . $id1 . ' - row was supposed to be updated.';
        $this->assertEmpty($row, $message);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testConstructor()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testConstructorThrowsAnInvalidArgumentExceptionWhenAnInvalidOptionIsSpecified()
    {
        $this->setupDb();

        $saveHandlerOptions = array(
            '123456' => 'I am an invalid option.',
        );

        $message = 'Invalid argument passed in Configuration:';

        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);


        $saveHandler = new TableGateway($this->db, new Configuration($saveHandlerOptions, true));
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setTableName
     * @covers Zend\Session\SaveHandler\TableGateway::setPrimary
     * @covers Zend\Session\SaveHandler\TableGateway::setTimestampColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setDataColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testConstructorWithDefaultConfigurationSpecified()
    {
        $this->saveHandlerConfiguration = new \Zend\Config\Config($this->saveHandlerOptions, true);
        $this->setupDb();
        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setTableName
     * @covers Zend\Session\SaveHandler\TableGateway::setPrimary
     * @covers Zend\Session\SaveHandler\TableGateway::setTimestampColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setDataColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testConstructorWithDifferentConfigurationSpecified()
    {
        $this->saveHandlerOptions['tableName'] = 'the_big_session_table';
        $this->saveHandlerOptions['primary'] = 'the_big_session_table_primary_key';
        $this->saveHandlerOptions['lifetime'] = 123;
        $this->saveHandlerOptions['timestampColumn'] = 'modified';
        $this->saveHandlerOptions['dataColumn'] = 'value';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();
        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getDataColumn
     */
    public function testGetDataColumn()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['dataColumn'], $saveHandler->getDataColumn());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getDataColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setDataColumn
     */
    public function testSetDataColumnWithAValidValue()
    {
        $this->saveHandlerOptions['dataColumn'] = 'the_big_session_table_data_column';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();
        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['dataColumn'], $saveHandler->getDataColumn());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getDataColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setDataColumn
     */
    public function testSetDataColumnWithAnEmptyValueAndThrowAnException()
    {
        $this->saveHandlerOptions['dataColumn'] = '';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();

        $message = 'The data column cannot be empty.';
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);

        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['dataColumn'], $saveHandler->getDataColumn());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testDefaultLifetimeUsesSessionGcMaxLifetime()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $maxLifetime = (integer) ini_get('session.gc_maxlifetime');
        $message = 'Lifetime must default to session.gc_maxlifetime.';

        $this->assertSame($maxLifetime, $saveHandler->getLifetime(), $message);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testResettingLifetime()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $lifetime = 12345;

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler->setLifetime($lifetime));

        $this->assertSame($lifetime, $saveHandler->getLifetime());

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler->resetLifetime());

        $maxLifetime = (integer) ini_get('session.gc_maxlifetime');
        $message = 'Lifetime must default to session.gc_maxlifetime.';

        $this->assertSame($maxLifetime, $saveHandler->getLifetime(), $message);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testSettingLifetimeWithAValidValue()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $lifetime = 12345;

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler->setLifetime($lifetime));

        $this->assertSame($lifetime, $saveHandler->getLifetime());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     */
    public function testSettingLifetimeWithNegativeValueAndThrowAnException()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $message = 'Lifetime must be greater than 0.';

        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);

        $lifetime = -1;

        $saveHandler->setLifetime($lifetime);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testTimestampIsEqualToTimeMinusTheLifetimePlusOneSecondAndSessionIsNotExpired()
    {
        $this->setupDb();
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $data = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => time() - $saveHandler->getLifeTime() + 1,
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data);

        $saveHandler->setSessionRow($data[ $this->saveHandlerOptions['primary'] ]);

        $this->assertFalse($saveHandler->isSessionExpired());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testTimestampIsEqualToTimeMinusTheLifetimeMinusOneSecondAndSessionIsExpired()
    {
        $this->setupDb();
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $data = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => time() - $saveHandler->getLifeTime() - 1,
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data);

        $saveHandler->setSessionRow($data[ $this->saveHandlerOptions['primary'] ]);

        $this->assertTrue($saveHandler->isSessionExpired());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testTimestampIsEqualToZeroAndSessionIsExpired()
    {
        $this->setupDb();
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $data = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => 0,
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data);

        $saveHandler->setSessionRow($data[ $this->saveHandlerOptions['primary'] ]);

        $this->assertTrue($saveHandler->isSessionExpired());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testTimestampIsEqualToTimeMinusTwiceTheLifetimeAndSessionIsExpired()
    {
        $this->setupDb();
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $data = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => time() - 2 * $saveHandler->getLifeTime(),
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data);

        $saveHandler->setSessionRow($data[ $this->saveHandlerOptions['primary'] ]);

        $this->assertTrue($saveHandler->isSessionExpired());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testTimestampIsEqualToNowAndSessionIsNotExpired()
    {
        $this->setupDb();
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $data = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => time(),
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        $this->table->insert($data);

        $saveHandler->setSessionRow($data[ $this->saveHandlerOptions['primary'] ]);
        //Debug::dump($saveHandler->getSessionRow(), eval('return __FILE__;') . "\$saveHandler->getSessionRow()");

        $this->assertFalse($saveHandler->isSessionExpired());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testTimestampIsEqualTo10SecondsAgoAndSessionIsNotExpired()
    {
        $this->setupDb();
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $data = array(
            $this->saveHandlerOptions['primary'] => md5(1),
            $this->saveHandlerOptions['timestampColumn']   => time() - 10,
            $this->saveHandlerOptions['dataColumn']        => '4',
        );
        //Debug::dump($data, eval('return __FILE__;') . "\$data");
        $this->table->insert($data);

        $saveHandler->setSessionRow($data[ $this->saveHandlerOptions['primary'] ]);
        //Debug::dump($saveHandler->getSessionRow(), eval('return __FILE__;') . "\$saveHandler->getSessionRow()");

        $this->assertFalse($saveHandler->isSessionExpired());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getPrimary
     */
    public function testGetPrimary()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['primary'], $saveHandler->getPrimary());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getPrimary
     * @covers Zend\Session\SaveHandler\TableGateway::setPrimary
     */
    public function testSetPrimaryWithAValidValue()
    {
        $this->saveHandlerOptions['primary'] = 'the_big_session_table_primary_key';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();
        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['primary'], $saveHandler->getPrimary());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getPrimary
     * @covers Zend\Session\SaveHandler\TableGateway::setPrimary
     */
    public function testSetPrimaryWithAnEmptyValueAndThrowAnException()
    {
        $this->saveHandlerOptions['primary'] = '';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();

        $message = 'The primary key cannot be empty.';
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);

        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['primary'], $saveHandler->getPrimary());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getPrimary
     * @covers Zend\Session\SaveHandler\TableGateway::setPrimary
     */
    public function testSetPrimaryWithAnArrayAndThrowAnException()
    {
        $this->saveHandlerOptions['primary'] = array('the_big_session_table_primary_key');
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();

        $message = 'The primary key must be a string. Multiple keys are not supported at this time.';
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);

        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getTableName
     */
    public function testGetTableName()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['tableName'], $saveHandler->getTableName());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getTableName
     * @covers Zend\Session\SaveHandler\TableGateway::setTableName
     */
    public function testSetTableNameWithAValidValue()
    {
        $this->saveHandlerOptions['tableName'] = 'the_big_session_table_name';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();
        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['tableName'], $saveHandler->getTableName());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getTableName
     * @covers Zend\Session\SaveHandler\TableGateway::setTableName
     */
    public function testSetTableNameWithAnEmptyValueAndThrowAnException()
    {
        $this->saveHandlerOptions['tableName'] = '';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();

        $message = 'The table name cannot be empty.';
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);

        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['tableName'], $saveHandler->getTableName());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getTimestampColumn
     */
    public function testGetTimestampColumn()
    {
        $this->setupDb();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['timestampColumn'], $saveHandler->getTimestampColumn());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getTimestampColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setTimestampColumn
     */
    public function testSetTimestampColumnWithAValidValue()
    {
        $this->saveHandlerOptions['timestampColumn'] = 'the_big_session_table_timestamp_column';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();
        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['timestampColumn'], $saveHandler->getTimestampColumn());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::getTimestampColumn
     * @covers Zend\Session\SaveHandler\TableGateway::setTimestampColumn
     */
    public function testSetTimestampColumnWithAnEmptyValueAndThrowAnException()
    {
        $this->saveHandlerOptions['timestampColumn'] = '';
        $this->saveHandlerConfiguration = new Configuration($this->saveHandlerOptions, true);
        $this->setupDb();

        $message = 'The timestamp column cannot be empty.';
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', $message);

        $saveHandler = new TableGateway($this->db, $this->saveHandlerConfiguration);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $this->assertSame($this->saveHandlerOptions['timestampColumn'], $saveHandler->getTimestampColumn());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::close
     */
    public function testCloseSessionShouldReturnTrue()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $this->assertTrue($saveHandler->close());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::destroy
     */
    public function testDestroy()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time(),
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertTrue($saveHandler->destroy($id));
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::destroy
     */
    public function testDestroySessionThatDoesNotExist()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $id = md5(1);

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertFalse($rowset->current());
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertFalse($saveHandler->destroy($id));
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::gc
     */
    public function testGarbageCollectionDoesDeleteSessionWithTimestampEqualToZero()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => 0,
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertTrue($saveHandler->gc());

        $id = md5(1);

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertEmpty($rowset->current());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::gc
     */
    public function testGarbageCollectionDoesNotDeleteSessionWithTimestampEqualToNow()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time(),
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertFalse($saveHandler->gc());

        $id = md5(1);

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertNotEmpty($rowset->current());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::gc
     */
    public function testGarbageCollectionDoesNotDeleteSessionWithTimestampEqualToNowMinusLifetimePlus10Seconds()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time()- $saveHandler->getLifetime() + 10,
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertFalse($saveHandler->gc());

        $id = md5(1);

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertNotEmpty($rowset->current());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::gc
     */
    public function testGarbageCollectionDoesDeleteSessionWithTimestampEqualToNowMinusLifetimeMinus10Seconds()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time()- $saveHandler->getLifetime() - 10,
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertTrue($saveHandler->gc());

        $id = md5(1);

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertEmpty($rowset->current());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::open
     * @covers Zend\Session\SaveHandler\TableGateway::getSessionSavePath
     * @covers Zend\Session\SaveHandler\TableGateway::getSessionName
     */
    public function testOpenSessionShouldReturnTrueAndSetsSessionNameAndSessionSavePath()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $sessionSavePath = 'here';
        $sessionName = 'there';
        $this->assertTrue($saveHandler->open($sessionSavePath, $sessionName));
        $this->assertSame($sessionSavePath, $saveHandler->getSessionSavePath());
        $this->assertSame($sessionName, $saveHandler->getSessionName());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::setSessionRow
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testReadSessionThatDoesExistAndIsNotExpiredBecauseTimestampIsNowAndReturnAString()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time(),
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));
        $row = $rowset->current();

        $this->assertNotEmpty($row);

        $dataColumn = $this->saveHandlerOptions['dataColumn'];

        $this->assertSame($data[ $dataColumn ], $row->$dataColumn);
        $this->assertSame($data[ $dataColumn ], $saveHandler->read($id));
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::setSessionRow
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     * @covers Zend\Session\SaveHandler\TableGateway::destroy
     */
    public function testReadSessionThatDoesExistButIsExpiredAndDestroySessionAndReturnAnEmptyString()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time()- $saveHandler->getLifetime() - 10,
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;') . "\$row");

        $this->assertSame('', $saveHandler->read($id));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertEmpty($rowset->current());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::read
     * @covers Zend\Session\SaveHandler\TableGateway::setSessionRow
     * @covers Zend\Session\SaveHandler\TableGateway::isSessionExpired
     */
    public function testReadSessionThatDoesNotExistAndReturnAnEmptyString()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);

        $this->assertSame('', $saveHandler->read($id));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));

        $this->assertEmpty($rowset->current());
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::write
     * @covers Zend\Db\TableGateway\TableGateway::insert
     * @covers Zend\Session\SaveHandler\TableGateway::getPrimary
     */
    public function testWriteSessionThatDoesNotExistAndInsertRowAndReturnTrue()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));
        $row = $rowset->current();

        $this->assertEmpty($row);

        $dataColumn = $this->saveHandlerOptions['dataColumn'];

        $writeData = 'counter|i:1;message|a:0:{}';

        $this->assertTrue($saveHandler->write($id, $writeData));

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));
        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;'));

        $this->assertNotEmpty($row);

        $this->assertSame($writeData, $row->$dataColumn);
    }

    /**
     * @covers Zend\Db\TableGateway\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::__construct
     * @covers Zend\Session\SaveHandler\TableGateway::setup
     * @covers Zend\Session\SaveHandler\TableGateway::getLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::resetLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::setLifetime
     * @covers Zend\Session\SaveHandler\TableGateway::write
     * @covers Zend\Db\TableGateway\TableGateway::update
     */
    public function testWriteSessionThatDoesExistAndUpdateRowAndReturnTrue()
    {
        $this->setupDb();

        // Set up table so we can pull directly from the database.
        $this->setupTable();
        $saveHandler = new TableGateway($this->db);
        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);
        //Debug::dump($saveHandler, eval('return __FILE__;'));

        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler);

        $id = md5(1);
        $data = array(
            $this->saveHandlerOptions['primary'] => $id,
            $this->saveHandlerOptions['timestampColumn']   => time(),
            $this->saveHandlerOptions['dataColumn']        => 'counter|i:2;message|a:0:{}',
        );
        $this->table->insert($data);

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));
        $row = $rowset->current();

        $this->assertNotEmpty($row);

        $dataColumn = $this->saveHandlerOptions['dataColumn'];

        $this->assertSame($data[ $dataColumn ], $row->$dataColumn);
        $writeData = 'counter|i:3;message|a:0:{}';

        // Set the session row so it can be picked up by $saveHandler->write()
        $this->assertInstanceOf('\Zend\Session\SaveHandler\TableGateway', $saveHandler->setSessionRow($id));

        $this->assertTrue($saveHandler->write($id, $writeData));

        $rowset = $this->table->select(array($this->saveHandlerOptions['primary'] => $id));
        //Debug::dump($rowset, eval('return __FILE__;'));
        $row = $rowset->current();
        //Debug::dump($row, eval('return __FILE__;'));

        $this->assertNotEmpty($row);

        $this->assertSame($writeData, $row->$dataColumn);
    }
}
