<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\DriverStatement\ParameterContainer,
    Zend\Db\Adapter\DriverStatement,
    Zend\Db\Adapter\Driver,
    Zend\Db\Adapter\Exception,
    PDO,
    PDOStatement;

class Statement implements DriverStatement
{
    /**
     * @var \Zend\Db\Adapter\Driver
     */
    protected $driver             = null;
    protected $sql                = false;
    protected $isQuery            = null;
    protected $parameterContainer = null;
    
    /**
     * @var \PDOStatement
     */
    protected $resource = null;
    
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function initialize(PDO $connectionResource, $sql)
    {
        $this->sql = $sql;
        if (strpos(ltrim($sql), 'SELECT') === 0) {
            $this->isQuery = true;
        }

        $this->resource = $connectionResource->prepare($sql);
        if ($this->resource == false) {
            $error = $connectionResource->errorInfo();
            throw new \Exception($error[2]);
        }
        return $this;
    }

//    public function setParameterContainer(ParameterContainer $parameterContainer)
//    {
//        $this->parameterContainer = $parameterContainer;
//    }

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
                $containerFactor = new \Zend\Db\Adapter\DriverStatement\ContainerFactory();
                $parameters = $containerFactor->createContainer($parameters);
            }
            if (!$parameters instanceof ParameterContainer) {
                throw new \InvalidArgumentException('ParameterContainer expected');
            }
            $this->bindParametersFromContainer($parameters);
        }

        if ($this->resource->execute() === false) {
            throw new Exception\InvalidQueryException($this->resource->error);
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }
    
    protected function bindParametersFromContainer(ParameterContainer $container)
    {
        $parameters = $container->toArray();
        foreach ($parameters as $position => &$value) {
            $type = PDO::PARAM_STR;
            if ($container->offsetHasErrata($position)) {
                switch ($container->offsetGetErrata($position)) {
                    case ParameterContainer::TYPE_INTEGER:
                        $type = PDO::PARAM_INT;
                        break;
                    case ParameterContainer::TYPE_NULL:
                        $type = PDO::PARAM_NULL;
                        break;
                    case ParameterContainer::TYPE_LOB:
                        $type = PDO::PARAM_LOB;
                        break;
                    case (is_bool($value)):
                        $type = PDO::PARAM_BOOL;
                        break;
                }
            }

            // position is named or positional, value is reference
            $this->resource->bindParam((is_int($position) ? ($position + 1) : $position), $value, $type);
        }
    }
}
