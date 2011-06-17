<?php

namespace Zend\Db\Adapter\Driver\Mysqli;


class Result implements \Iterator, \Zend\Db\Adapter\DriverResult
{
    const MODE_STATEMENT = 'statement';
    const MODE_RESULT = 'result';
    
    protected $mode = null;
    
    /**
     * @var Zend\Db\Adapter\Driver\AbstractDriver
     */
    protected $driver = null;
    
    /**
     * @var mysqli_result|mysqli_stmt
     */
    protected $resource = null;
    
    protected $pointerPosition = 0;
    protected $numberOfRows = -1;
    
    protected $currentComplete = false;
    protected $nextComplete = false;
    
    protected $currentData = false;
    
    protected $statementBindValues = array('keys' => null, 'values' => array());
    
    
    
    public function __construct(\Zend\Db\Adapter\Driver $driver, $resource, array $options = array())
    {
        $this->driver = $driver; 
        $this->resource = $resource;
        
        if (!$this->resource instanceof \mysqli_result && !$this->resource instanceof \mysqli_stmt) {
            throw new \InvalidArgumentException('Invalid resource provided.');
        }
        
        $this->mode = ($this->resource instanceof \mysqli_stmt) ? self::MODE_STATEMENT : self::MODE_RESULT;
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
        
        if ($this->mode == self::MODE_STATEMENT) {
            $this->loadDataFromMysqliStatement();
            return $this->currentData;
        } else {
            $this->loadFromMysqliResult();
            return $this->currentData;
        }
    }
    
    /**
     * Mysqli's binding and returning of statement values
     * 
     * Mysqli requires you to bind variables to the extension in order to 
     * get data out.  These values have to be references:
     * @see http://php.net/manual/en/mysqli-stmt.bind-result.php
     * 
     * @throws \RuntimeException
     */
    protected function loadDataFromMysqliStatement()
    {
        $data = null;
        // build the default reference based bind strutcure, if it does not already exist
        if ($this->statementBindValues['keys'] === null) {
            $this->statementBindValues['keys'] = array();
            $resultResource = $this->resource->result_metadata();
            foreach ($resultResource->fetch_fields() as $col) {
                $this->statementBindValues['keys'][] = $col->name;
            }
            $this->statementBindValues['values'] = array_fill(0, count($this->statementBindValues['keys']), null);
            $refs = array();
            foreach ($this->statementBindValues['values'] as $i => &$f) {
                $refs[$i] = &$f;
            }
            call_user_func_array(array($this->resource, 'bind_result'), $this->statementBindValues['values']);
        }
        
        if (($r = $this->resource->fetch()) === null) {
            return false;
        } elseif ($r === false) {
            throw new \RuntimeException($this->resource->error);
        }
        
        $this->currentData = array_combine($this->statementBindValues['keys'], $this->statementBindValues['values']);
        $this->currentComplete = true;
        $this->nextComplete = true;
        $this->pointerPosition++;
        return true;
    }
    
    protected function loadFromMysqliResult()
    {
        $this->currentData = null;
        
        if (($data = $this->resource->fetch_assoc()) === null) {
            return false;
        }
        
        $this->nextImpliedByFetch = true;
        $this->pointerPosition++;
        $this->data = $data;
        $this->currentComplete = true;
        $this->nextComplete = true;
        $this->pointerPosition++;
        return true;
    }
    
    public function next()
    {
        $this->currentComplete = false;
        
        if ($this->nextComplete == false) {
            $this->pointerPosition++;
        }
        
        $this->nextComplete = false;
    }
    
    public function key()
    {
        return $this->pointerPosition;
    }
    
    public function rewind()
    {
        $this->currentComplete = false;
        $this->pointerPosition = 0;
        if ($this->resource instanceof \mysqli_stmt) {
            //$this->resource->reset();
        } else {
            $this->resource->data_seek(0); // works for both mysqli_result & mysqli_stmt
        }
    }
    
    public function valid()
    {
        if ($this->currentComplete) {
            return true;
        }
        
        if ($this->mode == self::MODE_STATEMENT) {
            return $this->loadDataFromMysqliStatement();
        } else {
            return $this->loadFromMysqliResult();
        }
    }
    
    public function count()
    {
        // @todo return $this->numberOfRows;
    }
    
}