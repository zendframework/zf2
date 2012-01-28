<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\DriverResult,
    Iterator;

class Result implements Iterator, DriverResult
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

    protected $position = -1;

    public function setDriver(\Zend\Db\Adapter\Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function initialize($resource)
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
        
        $this->load();
        return $this->currentData;
    }
    
    public function next()
    {
        $this->load();
        return true;
    }
    
    protected function load($row = SQLSRV_SCROLL_NEXT)
    {
        $this->currentData = sqlsrv_fetch_array($this->resource, SQLSRV_FETCH_ASSOC, $row);
        $this->currentComplete = true;
        $this->position++;
    }
    
    public function key()
    {
        return $this->position;
    }
    
    public function rewind()
    {
        $this->position = 0;
        $this->load(SQLSRV_SCROLL_FIRST);
        return true;
    }
    
    public function valid()
    {
        if ($this->currentComplete && $this->currentData !== false) {
            return true;
        }

        $this->load();
        return ($this->currentData !== false);
    }
    
    public function count()
    {
        return sqlsrv_num_rows($this->resource);
    }

    public function isQueryResult()
    {
        // TODO: Implement isQueryResult() method.
    }

    public function getAffectedRows()
    {
        // TODO: Implement getAffectedRows() method.
    }
}