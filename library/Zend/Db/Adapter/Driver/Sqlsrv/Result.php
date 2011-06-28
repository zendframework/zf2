<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;


class Result implements \Iterator, \Zend\Db\Adapter\DriverResult
{
    /**
     * @var Zend\Db\Adapter\Driver\AbstractDriver
     */
    protected $driver = null;
    
    /**
     * @var Sqlsrv_result|Sqlsrv_stmt
     */
    protected $resource = null;
    
    protected $currentData = false;
    
    protected $currentComplete = false;
    protected $nextComplete = false;
    
    protected $pointerPosition = 0;
    protected $numberOfRows = -1;
    
    public function setDriver(\Zend\Db\Adapter\Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }
        
        $this->load(SQLSRV_SCROLL_NEXT);
        return $this->currentData;
    }
    
    public function next()
    {
        $this->load(SQLSRV_SCROLL_NEXT);
        return true;
    }
    
    protected function load($row = SQLSRV_SCROLL_NEXT)
    {
        $this->currentData = sqlsrv_fetch_array($this->resource, $row);
        $this->currentComplete = true;
        $this->pointerPosition++;
    }
    
    public function key()
    {
        return $this->pointerPosition;
    }
    
    public function rewind()
    {
        $this->pointerPosition = 0;
        $this->load(SQLSRV_SCROLL_FIRST);
        return true;
    }
    
    public function valid()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }
        
        return $this->load(SQLSRV_SCROLL_NEXT);
    }
    
    public function count()
    {
        return sqlsrv_num_rows($this->resource);
    }
    
}