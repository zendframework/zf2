<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\Adapter\DbSelect;

use Zend\Paginator\Adapter;
use Zend\Db\Statement\Oracle;
use Zend\Db\Statement\OracleException;
use ZendTest\Paginator\Adapter\DbSelectTest;

require_once __DIR__ . '/../../_files/TestTable.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class OracleTest extends DbSelectTest
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        $this->markTestIncomplete('Will skip until Zend\Db is refactored.');

        if (! extension_loaded('oci8')) {
            $this->markTestSkipped('Oci8 extension is not loaded');
        }

        if (! TESTS_ZEND_DB_ADAPTER_ORACLE_ENABLED) {
            $this->markTestSkipped('Oracle is required');
        }

        $this->_db = new \Zend\Db\Adapter\Oracle(
                array('host' => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME ,
                        'username' => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME ,
                        'password' => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD ,
                        'dbname' => TESTS_ZEND_DB_ADAPTER_ORACLE_SID));

        $this->_dropTable();
        $this->_createTable();
        $this->_populateTable();

        $this->_table = new \TestTable($this->_db);

        $this->_query = $this->_db->select()
                                  ->from('test')
                                  ->order('number ASC') // ZF-3740
                                  ->limit(1000, 0);     // ZF-3727

        $this->_adapter = new Adapter\DbSelect($this->_query);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->markTestIncomplete('Will skip until Zend\Db is refactored.');
        $this->_dropTable();
        $this->_db = null;
        $this->_adapter = null;
    }

    protected function _createTable ()
    {
        $this->_db->query(
                'create table "test" (
                               "number"      NUMBER(5),
                               "testgroup"   NUMBER(3),
                               constraint "pk_test" primary key ("number")
                           )');
        $this->_db->query(
                'create table "test_empty" (
                               "number"      NUMBER(5),
                               "testgroup"   NUMBER(3),
                               constraint "pk_test_empty" primary key ("number")
                           )');
    }

    protected function _populateTable ()
    {
        for ($i = 1; $i < 251; $i ++) {
            $this->_db->query('insert into "test" values (' . $i . ', 1)');
            $this->_db->query('insert into "test" values (' . ($i + 250) . ', 2)');
        }
    }

    protected function _dropTable ()
    {
        try {
            $this->_db->query('drop table "test"');
        } catch (OracleException $e) {}
        try {
            $this->_db->query('drop table "test_empty"');
        } catch (OracleException $e) {}
    }

    public function testGroupByQueryOnEmptyTableReturnsRowCountZero()
    {
        $query = $this->_db->select()
                           ->from('test_empty')
                           ->order('number ASC')
                           ->limit(1000, 0);
        $adapter = new Adapter\DbSelect($query);

        $this->assertEquals(0, $adapter->count());
    }
}
