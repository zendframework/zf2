<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Platform\Mysql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\ParameterContainer;

class CreateTableDecorator extends CreateTable implements PlatformDecoratorInterface
{
    /**
     * @var CreateTable
     */
    protected $subject;

    /**
     * @param CreateTable $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    protected function buildSqlString(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        // localize variables
        foreach (get_object_vars($this->subject) as $name => $value) {
            $this->{$name} = $value;
        }
        return parent::buildSqlString($platform, $driver, $parameterContainer);
    }

    protected function processColumns(PlatformInterface $platform = null)
    {
        if (!$this->columns) {
            return null;
        }
        $sqls = array();
        foreach ($this->columns as $i => $column) {
            $sql           = $this->processExpression($column, $platform);
            foreach ($column->getOptions() as $coName => $coValue) {
                switch (strtolower(str_replace(array('-', '_', ' '), '', $coName))) {
                    case 'identity':
                    case 'serial':
                    case 'autoincrement':
                        $sql .= ' AUTO_INCREMENT';
                        break;
                    /*
                    case 'primary':
                    case 'primarykey':
                        $sql .= ' PRIMARY KEY';
                        break;
                    case 'unique':
                    case 'uniquekey':
                        $sql .= ' UNIQUE KEY';
                        break;
                    */
                    case 'comment':
                        $sql .= ' COMMENT \'' . $coValue . '\'';
                        break;
                    case 'columnformat':
                    case 'format':
                        $sql .= ' COLUMN_FORMAT ' . strtoupper($coValue);
                        break;
                    case 'storage':
                        $sql .= ' STORAGE ' . strtoupper($coValue);
                        break;
                }
            }
            $sqls[$i] = $sql;
        }
        return array($sqls);
    }
}
