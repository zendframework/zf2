<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db\Operation;

/**
 * Operation for Truncating on setup or teardown of a database tester.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class Truncate implements \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     * @return void
     */
    public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        if(!($connection instanceof \Zend\Test\PHPUnit\Db\Connection)) {
            throw new \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException(
            	"Not a valid Zend_Test_PHPUnit_Db_Connection instance, ".get_class($connection)." given!"
            );
        }

        foreach ($dataSet->getReverseIterator() AS $table) {
            try {
                $tableName = $table->getTableMetaData()->getTableName();
                $this->_truncate($connection->getConnection(), $tableName);
            } catch (\Exception $e) {
                throw new \PHPUnit_Extensions_Database_Operation_Exception('TRUNCATE', 'TRUNCATE '.$tableName.'', array(), $table, $e->getMessage());
            }
        }
    }

    /**
     * Truncate a given table.
     *
     * @param \Zend\Db\Adapter\AbstractAdapter $db
     * @param string $tableName
     * @return void
     */
    protected function _truncate(\Zend\Db\Adapter\AbstractAdapter $db, $tableName)
    {
        $tableName = $db->quoteIdentifier($tableName);
        if($db instanceof \Zend\Db\Adapter\Pdo\Sqlite) {
            $db->query('DELETE FROM '.$tableName);
        } else if($db instanceof \Zend\Db\Adapter\Db2) {
            /*if(strstr(PHP_OS, "WIN")) {
                $file = tempnam(sys_get_temp_dir(), "zendtestdbibm_");
                file_put_contents($file, "");
                $db->query('IMPORT FROM '.$file.' OF DEL REPLACE INTO '.$tableName);
                unlink($file);
            } else {
                $db->query('IMPORT FROM /dev/null OF DEL REPLACE INTO '.$tableName);
            }*/
            throw \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException("IBM Db2 TRUNCATE not supported.");
        } else if($this->_isMssqlOrOracle($db)) {
            $db->query('TRUNCATE TABLE '.$tableName);
        } else if($db instanceof \Zend\Db\Adapter\Pdo\PgSql) {
            $db->query('TRUNCATE '.$tableName.' CASCADE');
        } else {
            $db->query('TRUNCATE '.$tableName);
        }
    }

    /**
     * Detect if an adapter is for Mssql or Oracle Databases.
     *
     * @param  \Zend\DB\Adapter\AbstractAdapter $db
     * @return bool
     */
    private function _isMssqlOrOracle($db)
    {
        return (
            $db instanceof \Zend\Db\Adapter\Pdo\Mssql ||
            $db instanceof \Zend\Db\Adapter\Sqlsrv ||
            $db instanceof \Zend\Db\Adapter\Pdo\Oci ||
            $db instanceof \Zend\Db\Adapter\Oracle
        );
    }
}
