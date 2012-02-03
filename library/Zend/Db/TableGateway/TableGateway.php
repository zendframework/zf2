<?php

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter,
    Zend\Db\ResultSet\ResultSet;

class TableGateway implements TableGatewayInterface
{
    const USE_STATIC_ADAPTER = null;

    /**
     * @var \Zend\Db\Adapter[]
     */
    protected static $staticAdapters = array();

    /**
     * @var \Zend\Db\Adapter
     */
    protected $adapter = null;

    /**
     * @var string
     */
    protected $tableName = null;

    /**
     * @var null|string
     */
    protected $databaseSchema = null;

    /**
     * @var null
     */
    protected $selectResultPrototype = null;

    public static function setStaticAdapter(Adapter $adapter)
    {
        $class = get_called_class();

        static::$staticAdapters[$class] = $adapter;
        if ($class === __CLASS__) {
            static::$staticAdapters[__CLASS__] = $adapter;
        }
    }

    public static function getStaticAdapter()
    {
        $class = get_called_class();

        // class specific adapter
        if (isset(static::$staticAdapters[$class])) {
            return static::$staticAdapters[$class];
        }

        // default adapter
        if (isset(static::$staticAdapters[__CLASS__])) {
            return static::$staticAdapters[__CLASS__];
        }

        throw new \Exception('No database adapter was found.');
    }

    public function __construct($tableName, Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $this->tableName = $tableName;
        if ($adapter === self::USE_STATIC_ADAPTER) {
            $adapter = static::getStaticAdapter();
        }
        $this->adapter = $adapter;
        if (is_string($databaseSchema)) {
            $this->databaseSchema = $databaseSchema;
        }
        $this->selectResultPrototype = ($selectResultPrototype) ?: new ResultSet;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function select($where)
    {
        // replace with Db\Sql select
        $adapter  = $this->adapter;
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();

        $sql = 'SELECT * FROM ';
        if ($this->databaseSchema != '') {
            $sql .= $platform->quoteIdentifier($this->databaseSchema)
                . $platform->getIdentifierSeparator();
        }

        $whereSql = $where;

        if (is_array($where)) {
            $whereSql = $parameters = array();
            foreach ($where as $whereName => $whereValue) {
                $whereParamName = $driver->formatParameterName($whereName);
                $whereSql[] = $platform->quoteIdentifier($whereName) . ' = ' . $whereParamName;
                $whereParameters[$whereParamName] = $whereValue;
            }
            $whereSql = implode(' AND ', $whereSql);
        }

        $sql .= $platform->quoteIdentifier($this->tableName)
            . ' WHERE ' . $whereSql;

        $statement = $driver->getConnection()->prepare($sql);
        $result = $statement->execute($whereParameters);

        // return result set
        $resultSet = clone $this->selectResultPrototype;
        $resultSet->setDataSource($result);
        return $resultSet;
    }

    public function insert($set)
    {
        // replace with Db\Sql select
        $adapter  = $this->adapter;
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();

        $sql = 'INSERT INTO ';
        if ($this->databaseSchema != '') {
            $sql .= $platform->quoteIdentifier($this->databaseSchema)
                . $platform->getIdentifierSeparator();
        }
        $sql .= $platform->quoteIdentifier($this->tableName);

        $setSql = $set;

        if (is_array($set)) {
            $setSqlColumns = $setSqlValues = $parameters = array();
            foreach ($set as $setName => $setValue) {
                $setParamName = $driver->formatParameterName($setName);
                $setSqlColumns[] = $platform->quoteIdentifier($setName);
                $setSqlValues[]  = $setParamName;
                $setParameters[$setParamName] = $setValue;
            }
            $setSql = '(' . implode(', ', $setSqlColumns) . ') VALUES (' . implode(', ', $setSqlValues) . ')';
        }

        $sql .= ' ' . $setSql;

        $statement = $driver->getConnection()->prepare($sql);
        $result = $statement->execute($setParameters);

        // return affected rows
        return $result->getAffectedRows();
    }

    public function update($set, $where)
    {
        // replace with Db\Sql select
        $adapter  = $this->adapter;
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();

        $sql = 'INSERT INTO ';
        if ($this->databaseSchema != '') {
            $sql .= $platform->quoteIdentifier($this->databaseSchema)
                . $platform->getIdentifierSeparator();
        }

        if (is_array($set)) {
            list($setSql, $setParameters) = $this->convertInputToSqlAndParameters($set, ', ');
        }

        $sql .= $platform->quoteIdentifier($this->tableName)
            . ' SET ' . $setSql;

        $statement = $driver->getConnection()->prepare($sql);
        $result = $statement->execute($setParameters);

        // return affected rows
        return $result->getAffectedRows();
    }

    public function delete($where)
    {
        // replace with Db\Sql select
        $adapter  = $this->adapter;
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();

        $sql = 'INSERT INTO ';
        if ($this->databaseSchema != '') {
            $sql .= $platform->quoteIdentifier($this->databaseSchema)
                . $platform->getIdentifierSeparator();
        }

        if (is_array($set)) {
            list($setSql, $setParameters) = $this->convertInputToSqlAndParameters($set, ', ');
        }

        $sql .= $platform->quoteIdentifier($this->tableName)
            . ' SET ' . $setSql;

        $statement = $driver->getConnection()->prepare($sql);
        $result = $statement->execute($setParameters);

        // return affected rows
        return $result->getAffectedRows();
    }

}
