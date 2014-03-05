<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\AdapterInterface;

abstract class AbstractPreparableSql extends AbstractSql implements PreparableSqlInterface
{
    /**
     * Prepare statement
     *
     * @param  AdapterInterface $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer = null)
    {
        $statement = $this->getSqlPlatform()->prepareStatement($this, $adapter, $statementContainer);
        if ($statement === null) {
            $statement = $this->processPrepareStatement($adapter, $statementContainer);
        }
        return $statement;
    }

    abstract protected function processPrepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer = null);

}
