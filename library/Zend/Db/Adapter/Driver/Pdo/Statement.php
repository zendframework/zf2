<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\DriverStatement\ParameterContainer,
    Zend\Db\Adapter\DriverStatement,
    Zend\Db\Adapter\Driver,
    Zend\Db\Adapter\Exception,
    PDO as PHPDataObject,
    PDOStatement;

class Statement implements DriverStatement
{
    /**
     * @var Zend\Db\Adapter\AbstractDriver
     */
    protected $driver             = null;
    protected $sql                = false;
    protected $isQuery            = null;
    protected $parameterContainer = null;
    
    /**
     * @var PDOStatement
     */
    protected $resource = null;
    
    public function __construct(Driver $driver, $resource, $sql)
    {
        $this->driver   = $driver;
        $this->resource = $resource;
        $this->sql      = $sql;
        
        if (!$this->resource instanceof PDOStatement) {
            throw new \InvalidArgumentException('Invalid statement type');
        }
        
        if (strpos(ltrim($sql), 'SELECT') === 0) {
            $this->isQuery = true;
        }
    }
    
    public function isQuery()
    {
        return $this->isQuery;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function getSql()
    {
        return $this->sql;
    }
    
    /**
     * @todo  Should this use the ability of PDOStatement to return objects of a specified class?
     * @param mixed $parameters 
     * @return void
     */
    public function execute($parameters = null)
    {
        if ($parameters != null) {
            if (is_array($parameters)) {
                throw new \Exception('Array parameters are not yet supported');
            }
            if (!$parameters instanceof ParameterContainer) {
                throw new \InvalidArgumentException('ParameterContainer expected');
            }
            $this->bindParametersFromContainer($parameters);
        }

        if ($this->resource->execute() === false) {
            throw new Exception\InvalidQueryException($this->resource->error);
        }

        $resultClass = $this->driver->getResultClass();
        $result      = new $resultClass($this->driver, $this->resource);
        
        return $result;
    }
    
    protected function bindParametersFromContainer(ParameterContainer $container)
    {
        foreach ($container as $position => &$value) {
            $type = PHPDataObject::PARAM_STRING;
            switch ($container->offsetGetErrata($position)) {
                case ParameterContainer::TYPE_INTEGER:
                    $type = PHPDataObject::PARAM_INT;
                    break;
                case ParameterContainer::TYPE_NULL:
                    $type = PHPDataObject::PARAM_NULL;
                    break;
                case ParameterContainer::TYPE_LOB:
                    $type = PHPDataObject::PARAM_LOB;
                    break;
                case (is_bool($value)):
                    $type = PHPDataObject::PARAM_BOOL;
                    break;
            }
            $this->resource->bindParam($position, $value, $type);
        }
    }
}
