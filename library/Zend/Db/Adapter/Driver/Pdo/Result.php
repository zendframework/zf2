<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver,
    Zend\Db\Adapter\DriverResult,
    Iterator,
    PDOStatement;

class Result implements Iterator, DriverResult
{
    const MODE_STATEMENT = 'statement';
    const MODE_RESULT    = 'result';
    
    protected $mode = null;
    
    /**
     * @var Zend\Db\Adapter\Driver\AbstractDriver
     */
    protected $driver = null;
    
    /**
     * @var mixed
     */
    protected $resource = null;
    
    public function __construct(Driver $driver, $resource, array $options = array())
    {
        $this->driver   = $driver;
        $this->resource = $resource;
        
        if (!$resource instanceof PDOStatement && !is_array($resource)) {
            throw new \InvalidArgumentException('Invalid resource provided.');
        }
        
        $this->mode = ($this->resource instanceof PDOStatement) ? self::MODE_STATEMENT : self::MODE_RESULT;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    /**
     * @todo   Should we allow passing configuration flags to the fetch() call?
     * @return void
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }
        
        if ($this->mode == self::MODE_STATEMENT) {
            return $this->resource->fetch();
        }

        return current($this->resource);
    }
    
    public function next()
    {
        if ($this->mode == self::MODE_RESULT) {
            return next($this->resource);
        }
    }
    
    public function key()
    {
        if ($this->mode == self::MODE_RESULT) {
            return key($this->resource);
        }
    }
    
    public function rewind()
    {
        if ($this->mode == self::MODE_RESULT) {
            return reset($this->resource);
        }
    }
    
    public function valid()
    {
        if ($this->mode == self::MODE_RESULT) {
            $key = key($this->resource);
            $valid = ($key !== NULL && $key !== FALSE);
            return $valid;
        }
    }
    
    public function count()
    {
        if ($this->mode == self::MODE_STATEMENT) {
            return $this->resource->rowCount();
        }
        return count($this->resource);
    }
    
}
