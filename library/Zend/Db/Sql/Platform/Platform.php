<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface,
    Zend\Db\Adapter\Driver\StatementInterface;

class Platform implements PlatformInterface
{

    protected $adapter = null;

    /**
     * @var PlatformInterface
     */
    protected $platform = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $platform = $adapter->getPlatform();
        switch (strtolower($platform->getName())) {
            case 'mysql':
                $this->platform = new Platform\Mysql\Mysql;
                break;
            default:
        }
    }

    public function supportsSqlObject(PreparableSqlInterface $sqlObject)
    {
        if ($this->platform) {
            return $this->platform->supportsSqlObject($sqlObject);
        }

        return true;
    }

    public function prepareStatementFromSqlObject(PreparableSqlInterface $sqlObject, StatementInterface $statement = null)
    {
        if ($this->platform) {
            return $this->prepareSqlObject($sqlObject);
        }

        $statement = ($statement) ?: $this->adapter->createStatement();
        $sqlObject->prepareStatement($this->adapter, $statement);
        return $statement;
    }

}
