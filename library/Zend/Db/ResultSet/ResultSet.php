<?php

namespace Zend\Db\ResultSet;

use Zend\Db\ResultSet\DataSource;

class ResultSet implements \Iterator, ResultSetInterface 
{
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY  = 'array';
    
    protected $rowClass = '\Zend\Db\ResultSet\RowObject';
    protected $returnType = self::TYPE_OBJECT;
    
    /**
     * @var \Zend\Db\ResultSet\DataSource\DataSourceInterface
     */
    protected $dataSource = null;
    
    
    public function __construct(DataSource\DataSourceInterface $dataSource)
    {
        if ($dataSource instanceof \Iterator) {
            $this->dataSource = $dataSource;
        } elseif ($dataSource instanceof \IteratorAggregate) {
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
    
    public function rewind()
    {
        return $this->dataSource->rewind();
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
