<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\DriverStatement\ParameterContainer;

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
     * @var \Sqlsrv_stmt
     */
    protected $resource = null;
    
    public function __construct(\Zend\Db\Adapter\Driver $driver, $resource, $sql)
    {
        $this->driver = $driver;
        $this->resource = $resource;
        $this->sql = $sql;
        
        if (!$this->resource instanceof \Sqlsrv_stmt) {
            throw new \InvalidArgumentException('Invalid resource type.');
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
        $result = new $resultClass($this->driver, $this->resource);
        
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
                    $value = null; // as per @see http://www.php.net/manual/en/Sqlsrv-stmt.bind-param.php#96148
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
