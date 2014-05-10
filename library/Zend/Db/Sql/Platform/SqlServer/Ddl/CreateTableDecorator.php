<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Platform\SqlServer\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;
use Zend\Db\Adapter\AdapterInterface;

class CreateTableDecorator extends CreateTable implements PlatformDecoratorInterface
{
    /**
     * @var CreateTable
     */
    protected $createTable;

    /**
     * @param CreateTable $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->createTable = $subject;
        return $this;
    }

    /**
     * Get the SQL string, based on the platform
     *
     * @param AdapterInterface $adapter
     * @param PlatformInterface $adapterPlatform
     * @return string
     */
    protected function processGetSqlString(AdapterInterface $adapter, PlatformInterface $adapterPlatform)
    {
        // localize variables
        foreach (get_object_vars($this->createTable) as $name => $value) {
            $this->{$name} = $value;
        }
        return parent::processGetSqlString($adapter, $adapterPlatform);
    }

    /**
     * @param AdapterInterface $adapter
     * @param null|PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processTable(AdapterInterface $adapter, PlatformInterface $adapterPlatform = null)
    {
        $ret = array('');
        if ($this->isTemporary) {
            $table = '#';
        } else {
            $table = '';
        }
        $ret[] = $adapterPlatform->quoteIdentifier($table . ltrim($this->table, '#'));
        return $ret;
    }
}
