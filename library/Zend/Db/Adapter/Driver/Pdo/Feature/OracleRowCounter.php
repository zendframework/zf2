<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Pdo\Feature;

use Zend\Db\Adapter\Driver\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\Pdo;

/**
 * OracleRowCounter
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class OracleRowCounter extends AbstractFeature
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'OracleRowCounter';
    }

    /**
     * @param \Zend\Db\Adapter\Driver\Pdo\Statement $statement
     * @return int
     */
    public function getCountForStatement(Pdo\Statement $statement)
    {
        $countStmt = clone $statement;
        $sql = $statement->getSql();
        if ($sql == '' || stripos($sql, 'select') === false) {
            return null;
        }
        $countSql = 'SELECT COUNT(*) as "count" FROM (' . $sql . ')';
        $countStmt->prepare($countSql);
        $result = $countStmt->execute();
        $countRow = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        unset($statement, $result);
        return $countRow['count'];
    }

    /**
     * @param $sql
     * @return null|int
     */
    public function getCountForSql($sql)
    {
        if (!stripos($sql, 'select')) {
            return null;
        }
        $countSql = 'SELECT COUNT(*) as count FROM (' . $sql . ')';
        /** @var $pdo \PDO */
        $pdo = $this->pdoDriver->getConnection()->getResource();
        $result = $pdo->query($countSql);
        $countRow = $result->fetch(\PDO::FETCH_ASSOC);
        return $countRow['count'];
    }

    /**
     * @param $context
     * @return closure
     */
    public function getRowCountClosure($context)
    {
        $oracleRowCounter = $this;
        return function () use ($oracleRowCounter, $context) {
            /** @var $oracleRowCounter OracleRowCounter */
            return ($context instanceof Pdo\Statement)
                ? $oracleRowCounter->getCountForStatement($context)
                : $oracleRowCounter->getCountForSql($context);
        };
    }

}
