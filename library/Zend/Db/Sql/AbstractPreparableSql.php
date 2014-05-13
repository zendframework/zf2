<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;

abstract class AbstractPreparableSql extends AbstractSql implements PreparableSqlInterface
{
    /**
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
     * @return StatementContainerInterface
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer = null)
    {
        $statementContainer = $statementContainer ?: $adapter->getDriver()->createStatement();
        $sqlPlatform = $adapter->getSqlPlatform();

        if ($this instanceof PlatformDecoratorInterface) {
            $this->processPrepareStatement($adapter, $statementContainer);
            return $statementContainer;
        }

        if ($sqlPlatform->getSubject() === $this) {
            $this->processPrepareStatement($adapter, $statementContainer);
            return $statementContainer;
        }

        $sqlPlatform->setSubject($this)->prepareStatement($adapter, $statementContainer);
        return $statementContainer;
    }

    /**
     * Prepare statement
     *
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
     * @return void
     */
    abstract protected function processPrepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer);
}
