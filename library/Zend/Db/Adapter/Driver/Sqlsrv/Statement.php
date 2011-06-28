<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\DriverStatement;

class Statement implements \Zend\Db\Adapter\DriverStatement
{

    /**
     * @var Zend\Db\Adapter\AbstractDriver
     */
    protected $driver = null;
    protected $sql = false;
    protected $isQuery = null;
    protected $parameterReferences = array();
    
    /**
     * @var Zend\Db\Adapter\DriverStatement\ParameterContainer
     */
    protected $parameterContainer = null;
    
    /**
     * @var \Sqlsrv_stmt
     */
    protected $resource = null;
    
    public function setDriver(\Zend\Db\Adapter\Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    /**
     * 
     * One of two resource types will be provided here:
     * a) "SQL Server Connection" when a prepared statement needs to still be produced
     * b) "SQL Server Statement" when a prepared statement has been already produced 
     * (there will need to already be a bound param set if it applies to this query)
     * 
     * @param resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
    
    public function setSql($sql)
    {
        if (strpos(ltrim($sql), 'SELECT') === 0) {
            $this->isQuery = true;
        }
        $this->sql = $sql;
        return $this;
    }
    
    public function setParameterContainer(DriverStatement\ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
    }
    
    public function isQuery()
    {
        return $this->isQuery;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function getSQL()
    {
        return $this->sql;
    }
    
    public function execute($parameters = null)
    {
        if ($parameters !== null) {
            if ($parameters instanceof DriverStatement\ParameterContainer) {
                $this->setParameterContainer($parameters);
            } else {
                $pContainerFactory = new DriverStatement\ContainerFactory();
                $this->setParameterContainer($pContainerFactory->createContainer($parameters));
                unset($pContainerFactory);
            }
        }

        if ($this->parameterContainer) {
            $this->bindParametersFromContainer();
        }
        
        // delayed prepare, this is the case since we allow parameters to be supplied at execution time
        if (get_resource_type($this->resource) == 'SQL Server Connection') {
            $this->resource = sqlsrv_prepare($this->resource, $this->sql, $this->parameterReferences);
        }
            
        if (sqlsrv_execute($this->resource) === false) {
            $ee = new ErrorException(sqlsrv_errors());
            throw new \Zend\Db\Adapter\Exception\InvalidQueryException('Invalid query', null, $ee);
        }

        $resultClass = $this->driver->getResultClass();
        $result = new $resultClass();
        $result->setDriver($this->driver);
        $result->setResource($this->resource);
        
        return $result;
    }
    
    protected function bindParametersFromContainer()
    {
        if (!$this->parameterReferences) {
            $refArray = array(null, SQLSRV_PARAM_IN, null, null);
            $this->parameterReferences = array_pad(array(), $this->parameterContainer->count(), $refArray);
        }
        
        foreach ($this->parameterReferences as $index => &$positionInfo) {
            $positionInfo[0] = $this->parameterContainer->offsetGet($index);
            if ($this->parameterContainer->offsetHasErrata($index)) {
                $positionInfo[3] = $this->parameterContainer->offsetGetErrata($index);
            }
        }
    }
    
}
