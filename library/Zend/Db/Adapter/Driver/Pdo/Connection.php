<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter,
    Zend\Db\Adapter\Driver,
    Zend\Db\Adapter\Exception\InvalidQueryException,
    PDO as PHPDataObject,
    PDOException,
    PDOStatement;


class Connection implements Adapter\DriverConnection
{
    /**
     * @var AbstractDriver
     */
    protected $driver = null;
    
    protected $connectionParams = array();
    
    /**
     * @var PHPDataObject
     */
    protected $resource = null;

    protected $inTransaction = false;
    
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function setConnectionParams(array $connectionParams)
    {
        $this->connectionParams = $connectionParams;
    }
    
    public function getConnectionParams()
    {
        return $this->connectionParams;
    }
    
    public function getDefaultCatalog()
    {
        return null;
    }
    
    public function getDefaultSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $result = $this->resource->query('SELECT DATABASE()');
        $r = $result->fetch_row();
        return $r[0];
    }
    
    /**
     * @return PHPDataObject
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    public function connect()
    {
        if ($this->resource) {
            return;
        }

        $dsn = $username = $password = null;
        $options = array();
        foreach ($this->connectionParams as $key => $value) {
            switch ($key) {
                case 'dsn':
                    $dsn = (string) $value;
                    break;
                case 'username':
                    $username = (string) $value;
                    break;
                case 'password':
                    $password = (string) $value;
                    break;
                case 'options':
                    $options = array_merge($options, (array) $value);
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }

        try {
            $this->resource = new PHPDataObject($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \Exception('Connect Error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    public function isConnected()
    {
        return ($this->resource instanceof PHPDataObject);
    }
    
    public function disconnect()
    {
        if ($this->isConnected()) {
            unset($this->resource);
        }
    }
    
    public function beginTransaction()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        $this->resource->beginTransaction();
        $this->inTransaction = true;
    }
    
    public function commit()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $this->resource->commit();
        
        $this->inTransaction = false;
    }
    
    public function rollback()
    {
        if (!$this->isConnected()) {
            throw new \Exception('Must be connected before you can rollback');
        }
        
        if (!$this->inTransaction) {
            throw new \Exception('Must call commit() before you can rollback');
        }
        
        $this->resource->rollBack();
        return $this;
    }
    
    
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $resultClass = $this->driver->getResultClass();
        
        $returnValue = $this->resource->query($sql);
        
        // if the returnValue is boolean false, bypass wrapping it
        if (false !== $returnValue) {
            $result = new $resultClass($this->driver, array(), $returnValue);
            return $result;
        } elseif ($returnValue === false) {
            $errorInfo = $this->resource->errorInfo();
            throw new InvalidQueryException($errorInfo[2]);
        }
        
        return $returnValue;
    }
    
    /**
     * @todo PDO_SQLite does not support scrollable cursors; make this configurable based on dsn?
     */
    public function prepare($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $stmtResource = $this->resource->prepare($sql, array(
            PHPDataObject::ATTR_CURSOR => PHPDataObject::CURSOR_SCROLL,
        ));
        
        if (!$stmtResource instanceof PDOStatement) {
            throw new \RuntimeException('Statement not produced');
        }
        
        $statementClass = $this->driver->getStatementClass();
        $statement = new $statementClass();
        $statement->setDriver($this->driver)
                  ->setResource($stmtResource)
                  ->setSql($sql);
        return $statement;
    }

}
