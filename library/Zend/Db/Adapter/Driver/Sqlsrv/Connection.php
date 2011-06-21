<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;
use Zend\Db\Adapter;


class Connection implements Adapter\DriverConnection
{
    /**
     * @var Zend\Db\Adapter\Driver\AbstractDriver
     */
    protected $driver = null;
    
    protected $connectionParams = array();
    
    /**
     * @var \Sqlsrv
     */
    protected $resource = null;

    protected $inTransaction = false;
    
    /*
    public function __construct(Adapter\AbstractDriver $driver, array $connectionParameters)
    {
        $this->driver = $driver;
        $this->connectionParams = $connectionParameters;
    }
    */
    
    public function setDriver(Adapter\Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function setConnectionParams(array $connectionParameters)
    {
        $this->connectionParams = $connectionParameters;
        return $this;
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
     * @return \Sqlsrv
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
        
        $serverName = '.';
        $params = array();
        foreach ($this->connectionParams as $cpName => $cpValue) {
            switch (strtolower($cpName)) {
                case 'hostname':
                case 'servername':
                    $serverName = $cpValue;
                    break;
                // @todo check other sqlsrv param values
                default:
                    $params[$cpName] = $cpValue;
            }
        }
        
        $this->resource = sqlsrv_connect($serverName, $params);
        
        if (!$this->resource) {
            $prevErrorException = new ErrorException(sqlsrv_errors());
            throw new \Exception('Connect Error', null, $prevErrorException);
        }

    }
    
    public function isConnected()
    {
        return (is_resource($this->resource));
    }
    
    public function disconnect()
    {
        sqlsrv_close($this->resource);
        unset($this->resource);
    }
    
    public function beginTransaction()
    {
        // http://msdn.microsoft.com/en-us/library/cc296151.aspx
        /*
        $this->resource->autocommit(false);
        $this->inTransaction = true;
        */
    }
    
    public function commit()
    {
        // http://msdn.microsoft.com/en-us/library/cc296194.aspx
        /*
        if (!$this->resource) {
            $this->connect();
        }
        
        $this->resource->commit();
        
        $this->inTransaction = false;
        */
    }
    
    public function rollback()
    {
        // http://msdn.microsoft.com/en-us/library/cc296176.aspx
        /*
        if (!$this->resource) {
            throw new \Exception('Must be connected before you can rollback.');
        }
        
        if (!$this->_inCommit) {
            throw new \Exception('Must call commit() before you can rollback.');
        }
        
        $this->resource->rollback();
        return $this;
        */
    }
    
    
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $resultClass = $this->driver->getResultClass();
        
        $returnValue = sqlsrv_query($this->resource, $sql);
        
        // if the returnValue is something other than a Sqlsrv_result, bypass wrapping it
        if (is_resource($returnValue)) {
            $result = new $resultClass($this->driver, array(), $returnValue);
            $result->setDriver($this->driver);
            // @todo how do we get results into this thing?
            return $result;
        } elseif ($returnValue === false) {
            throw new \Zend\Db\Adapter\Exception\InvalidQueryException(sqlsrv_error());
        }
        
        return $returnValue;
    }
    
    public function prepare($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $stmtResource = sqlsrv_prepare($this->resource, $sql);

        if (!is_resource($stmtResource)) {
            $prevErrorException = new ErrorException(sqlsrv_errors());
            throw new \RuntimeException('Statement not produced', null, $prevErrorException);
        }
        
        $statementClass = $this->driver->getStatementClass();
        $statement = new $statementClass();
        $statement->setDriver($this->driver);
        $statement->setResource($stmtResource);
        $statement->setSql($sql);
        return $statement;
    }

}
    