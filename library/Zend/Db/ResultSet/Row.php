<?php

namespace Zend\Db\ResultSet;

class Row implements RowObject
{
    const KEY_POSITION = 'position';
    const KEY_NAME     = 'name';
    
    protected $_data = null;
    
    public function __construct(array $data)
    {
        $this->_data = $data;
    }
    
    public function offsetExists($offset)
    {
        
    }
    
    public function offsetGet($offset)
    {
        
    }
    
    public function offsetSet($offset, $value)
    {
        
    }
    
    public function offsetUnset($offset)
    {
        
    }
    
    public function count()
    {
        return count($this->_data);
    }
}

