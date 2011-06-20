<?php

namespace Zend\Db\ResultSet;

interface ResultCollection extends \Countable, \Traversable
{
    public function getFieldCount();
    
}
