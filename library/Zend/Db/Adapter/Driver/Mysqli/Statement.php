<?php

namespace Zend\Db\Adapter\Driver\Mysqli;

use Zend\Db\Adapter,
    Zend\Db\Adapter\DriverStatement\ParameterContainer;

class Statement implements \Zend\Db\Adapter\DriverStatement
{
    /**
     * @var Zend\Db\Adapter\AbstractDriver
     */
    protected $driver = null;
    protected $sql = false;
    protected $isQuery = null;
    protected $parameterContainer = null;
    
    /**
     * @var \mysqli_stmt
     */
    protected $resource = null;
    
    public function setDriver(Adapter\Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function setResource($resource)
    {
        if (!$resource instanceof \mysqli_stmt) {
            throw new \InvalidArgumentException('Invalid resource type.');
        }
        
        $this->resource = $resource;
        return $this;
    }
    
    public function setSql($sql)
    {
        $this->sql = $sql;
        if (strpos(ltrim($sql), 'SELECT') === 0) {
            $this->isQuery = true;
        }
        return $this;
    }
    
    public function setParameterContainer(ParameterContainer $parameterContainer)
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
        if ($parameters != null) {
            
            if (is_array($parameters)) {
                die('todo');
            }
            if (!$parameters instanceof ParameterContainer) {
                throw new \InvalidArgumentException('ParameterContainer expected');
            }
            $this->bindParametersFromContainer($parameters);
        }
            
        if ($this->resource->execute() === false) {
            throw new \Zend\Db\Adapter\Exception\InvalidQueryException($this->resource->error);
        }

        $resultClass = $this->driver->getResultClass();
        $result = new $resultClass();
        $result->setDriver($this->driver);
        $result->setResource($this->resource);
        
        return $result;
    }
    
    protected function bindParametersFromContainer(ParameterContainer $pContainer)
    {
        $clonedPContainer = clone $pContainer;
        
        $type = '';
        $args = array();

        foreach ($clonedPContainer as $position => &$value) {
            switch ($pContainer->offsetGetErrata($position)) {
                case ParameterContainer::TYPE_DOUBLE:
                    $type .= 'd';
                    break;
                case ParameterContainer::TYPE_NULL:
                    $value = null; // as per @see http://www.php.net/manual/en/mysqli-stmt.bind-param.php#96148
                case ParameterContainer::TYPE_INTEGER:
                    $type .= 'i';
                    break;
                case ParameterContainer::TYPE_STRING:
                default:
                    $type .= 's';
                    break;
            }
            array_push($args, $value);
        }
        array_unshift($args, $type);
        
        call_user_func_array(array($this->resource, 'bind_param'), $args);
    }
}
