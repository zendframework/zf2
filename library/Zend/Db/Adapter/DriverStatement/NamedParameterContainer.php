<?php

namespace Zend\Db\Adapter\DriverStatement;

class NamedParameterContainer implements ParameterContainer
{
    const ARRAY_IS_NAMES = 'names';
    const ARRAY_IS_NAMES_AND_VALUES = 'namesAndValues';
    
    protected $values = array();
    protected $errata = array();
    
    public function __construct($array = null, $arrayMode = self::ARRAY_IS_NAMES)
    {
        if ($array !== null && !is_array($array)) {
            throw new \InvalidArgumentException('array parameter must be an array');
        }
        
        if ($array && $arrayMode === self::ARRAY_IS_NAMES) {
            $this->setNames($array);
        } elseif ($array && $arrayMode === self::ARRAY_IS_NAMES_AND_VALUES) {
            $this->setNames(array_keys($array));
            $this->setFromArray($array);
        }
    }
    
    public function setNames(Array $names)
    {
        foreach ($names as $name) {
            $this->values[$name] = null;
        }
    }
    
    public function offsetSet($name, $value, $errata = null)
    {

    }
    
    public function offsetGet($name)
    {
    }
    
    public function offsetExists($name)
    {
    }
    public function offsetUnset($name)
    {
    }
    
    public function setFromArray(Array $array)
    {
        if (count($array) === 0) {
            return;
        }

        foreach ($array as $name => $value) {
            $this->offsetSet($name, $value);
        }
        return $this;
    }

    public function offsetSetErrata($name, $errata)
    {
        if (!array_key_exists($name, $this->values)) {
            throw new \InvalidArgumentException('A value for the name must exist before assigning errata');
        }
        $this->errata[$name] = $errata;
    }
    
    public function offsetSetErrata($name, $errata)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata');
        }
    }
    
    public function offsetGetErrata($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata');
        }
        return (isset($this->errata[$name])) ?: null;
    }

    public function offsetHasErrata($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata');
        }
        return (isset($this->errata[$name]) && $this->errata[$name] !== null);
    }
    
    public function offsetUnsetErrata($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata');
        }
        unset($this->errata[$name]);
    }
    
    public function getErrataIterator()
    {
        return new \ArrayIterator($this->errata);
    }
    
    public function count()
    {
        return count($this->values);
    }
    
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }
    
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }
    
    public function __unset($name)
    {
        return $this->offsetUnset($name);
    }

}
