<?php

namespace Zend\Db\Adapter;

abstract class AbstractDriver implements Driver
{
    protected $adapter = null;
    protected $connectionClass = null;
    protected $statementClass = null;
    protected $resultClass = null;
    protected $connectionParams = array();
    protected $statementParams = array();
    protected $resultParams = array();
    
    protected $connection = null;

    public function __construct($options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }
        
        if ($this->getConnectionClass() == null 
            || $this->getStatementClass() == null
            || $this->getResultClass() == null
            ) {
            throw new \Exception('This extension wrapper does not have a connection, statement, or result class set.');
        }
        
        $this->checkEnvironment();
    }
    
    public function setOptions(array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            if (method_exists($this, 'set' . $optionName)) {
                $this->{'set' . $optionName}($optionValue);
            }
        }
    }
    
    public function setAdapter(\Zend\Db\Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
    
    public function setConnectionClass($connectionClass)
    {
        $this->connectionClass = $connectionClass;
        return $this;
    }
    
    public function getConnectionClass()
    {
        return $this->connectionClass;
    }
    
    public function setConnectionParams($connectionParams)
    {
        $this->connectionParams = $connectionParams;
        return $this;
    }
    
    public function getConnectionParams()
    {
        return $this->connectionParams;
    }
    
    
    public function setStatementClass($statementClass)
    {
        $this->statementClass = $statementClass;
        return $this;
    }

    public function getStatementClass()
    {
        return $this->statementClass;
    }
    
    public function setStatementParams($statementParams)
    {
        $this->_statementParams = $statementParams;
        return $this;
    }
    
    public function getStatementParams()
    {
        return $this->_statementParams;
    }
    
    
    public function setResultClass($resultClass)
    {
        $this->resultClass = $resultClass;
        return $this;
    }

    public function getResultClass()
    {
        return $this->resultClass;
    }
    
    public function setResultParams($resultClass)
    {
        $this->resultClass = $resultClass;
        return $this;
    }
    
    public function getResultParams()
    {
        return $this->resultClass;
    }
    
    public function getPrepareTypeSupport()
    {
        return array(\Zend\Db\Adapter::PREPARE_TYPE_POSITIONAL);
    }
    
    public function formatNamedParameter($name)
    {
        throw new \Exception('This Driver does not support named parameters');
    }

    /**
     * setConnection()
     * 
     * @param $connection
     */
    public function setConnection(DriverConnection $connection)
    {
        $this->connection = $connection;
        return $this;
    }
    
    /**
     * getConnection()
     * 
     * This method will attempt to lazy-load the connection object if
     * if does not already exist in the adatper.
     * 
     * @return \Zend\Db\Adapter\DriverConnection
     */
    public function getConnection()
    {
        if ($this->connection == null) {
            $connectionClass = $this->getConnectionClass();
            $connection = new $connectionClass;
            $connection->setDriver($this);
            $connection->setConnectionParams($this->getConnectionParams());
            $this->setConnection($connection);
        }
        return $this->connection;
    }

    
    
}
