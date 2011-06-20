<?php

namespace Zend\Db\ResultSet;

use Iterator,
    IteratorAggregate;

class ResultSet implements Iterator, ResultCollection
{
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY  = 'array';
    
    protected $rowClass   = '\Zend\Db\ResultSet\Row';
    protected $returnType = static::TYPE_OBJECT;
    
    /**
     * @var \Zend\Db\ResultSet\DataSource
     */
    protected $dataSource = null;
    
    
    public function __construct(DataSource $dataSource)
    {
        if ($dataSource instanceof Iterator) {
            $this->dataSource = $dataSource;
        } elseif ($dataSource instanceof IteratorAggregate) {
            $this->dataSource->getIterator();
        } else {
            throw new \Exception('DataSource provided implements proper interface but does not implement \Iterator nor \IteratorAggregate');
        }
    }
    
    public function getFieldCount() {}
    
    public function next()
    {
        return $this->dataSource->next();
    }
    
    
    public function key()
    {
        return $this->dataSource->key();
    }
    
    public function current()
    {
        return $this->dataSource->current();
    }
    
    public function valid()
    {
        return $this->dataSource->valid();
    }
    
    public function count()
    {
        return $this->dataSource->count();
    }

    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            if (is_array($row)) {
                $return[] = $row;
            } elseif (method_exists('toArray', $row)) {
                $return[] = $row->toArray();
            } else {
                throw new \RuntimeException('Rows as part of this datasource cannot be cast to an array.');
            }
        }
        return $return;
    }
    
    
}
