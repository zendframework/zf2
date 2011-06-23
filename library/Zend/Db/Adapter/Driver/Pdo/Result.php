<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver,
    Zend\Db\Adapter\DriverResult,
    Iterator,
    PDO as PHPDataObject,
    PDOStatement;

/**
 * Resultset for PDO
 *
 * @todo Use PDO's native interface for fetching into named objects?
 */
class Result implements Iterator, DriverResult
{
    const MODE_STATEMENT = 'statement';
    const MODE_RESULT    = 'result';
    
    /**
     * What type of result are we iterating over? Uses the MODE_* constants
     * @var string
     */
    protected $mode = null;

    /**
     * Fetch style; defaults to PDO::FETCH_BOTH
     * @var int
     */
    protected $fetchStyle = PHPDataObject::FETCH_BOTH;
    
    /**
     * @var Zend\Db\Adapter\Driver\AbstractDriver
     */
    protected $driver = null;
    
    /**
     * @var mixed
     */
    protected $resource = null;

    /**
     * @var array Result options
     */
    protected $options;

    /**
     * Track current item in recordset
     * @var mixed
     */
    protected $currentData;

    /**
     * Current position of scrollable statement
     * @var int
     */
    protected $position = -1;

    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function setResource($resource)
    {
        if (!$resource instanceof PDOStatement && !is_array($resource)) {
            throw new \InvalidArgumentException('Invalid resource provided.');
        }
        $this->resource = $resource;
        $this->mode = ($this->resource instanceof PDOStatement) ? self::MODE_STATEMENT : self::MODE_RESULT;
        return $this;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
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
        if ($this->mode == self::MODE_STATEMENT) {
            // Handle first iteration, if necessary
            if (-1 === $this->position && $this->count()) {
                $this->currentData = $this->resource->fetch($this->fetchStyle, PHPDataObject::FETCH_ORI_FIRST);
                $this->position    = 0;
            }
            return $this->currentData;
        }
        // MODE_RESULT
        return current($this->resource);
    }
    
    public function next()
    {
        if ($this->mode == self::MODE_STATEMENT) {
            $this->currentData  = $this->resource->fetch($this->fetchStyle, PHPDataObject::FETCH_ORI_NEXT);
            $this->position++;
            return $this->currentData;
        }
        // MODE_RESULT
        return next($this->resource);
    }
    
    public function key()
    {
        if ($this->mode == self::MODE_STATEMENT) {
            return $this->position;
        }
        // MODE_RESULT
        return key($this->resource);
    }
    
    public function rewind()
    {
        if ($this->mode == self::MODE_STATEMENT) {
            $this->currentData = $this->resource->fetch($this->fetchStyle, PHPDataObject::FETCH_ORI_FIRST);
            $this->position    = 0;
            return $this->currentData;
        }
        // MODE_RESULT
        return reset($this->resource);
    }
    
    public function valid()
    {
        if ($this->mode == self::MODE_STATEMENT) {
            return ($this->position < $this->count());
        }
        // MODE_RESULT
        $key   = key($this->resource);
        $valid = ($key !== NULL && $key !== FALSE);
        return $valid;
    }
    
    public function count()
    {
        if ($this->mode == self::MODE_STATEMENT) {
            return $this->resource->rowCount();
        }
        // MODE_RESULT
        return count($this->resource);
    }
    
}
