<?php

namespace Zend\Db\Adapter\DriverStatement;

interface ParameterContainer extends \ArrayAccess, \Countable, \Traversable
{
    const TYPE_AUTO = 'auto';
    const TYPE_NULL = 'null';
    const TYPE_DOUBLE = 'double';
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING = 'string';
    const TYPE_LOB = 'lob';
    
    public function __construct($definition = null);
    
    public function setFromArray(Array $values);
    
    public function offsetSetErrata($offset, $errata);
    public function offsetGetErrata($offset);
    public function offsetHasErrata($offset);
    public function offsetUnsetErrata($offset);
    public function getErrataIterator();

    public function toArray();
}
