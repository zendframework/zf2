<?php

namespace Zend\Di;

use SplDoublyLinkedList;

class DefinitionList extends SplDoublyLinkedList implements Definition\Definition
{

    /**
     * __construct function.
     * 
     * @access public
     * @param mixed $definitions
     * @return void
     */
    public function __construct($definitions)
    {
        if (!is_array($definitions)) {
            $definitions = array($definitions);
        }
        foreach ($definitions as $definition) {
            $this->push($definition);
        }
    }
    
    /**
     * addDefinition function.
     * 
     * @access public
     * @param mixed Definition\Definition $definition
     * @param bool $addToBackOfList. (default: true)
     * @return void
     */
    public function addDefinition(Definition\Definition $definition, $addToBackOfList = true)
    {
        if ($addToBackOfList) {
            $this->push($definition);
        } else {
            $this->unshift($definition);
        }
    }

    /**
     * @param string $type
     * @return Definition[]
     */
    public function getDefinitionsByType($type)
    {
        $definitions = array();
        foreach ($this as $definition) {
            if ($definition instanceof $type) {
                $definitions[] = $definition;
            }
        }
        return $definitions;
    }

    /**
     * @param string $type
     * @return Definition
     */
    public function getDefinitionByType($type)
    {
        foreach ($this as $definition) {
            if ($definition instanceof $type) {
                return $definition;
            }
        }
        return false;
    }

    /**
     * getDefinitionForClass function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function getDefinitionForClass($class)
    {
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                return $definition;
            }
        }
        return false;
    }
    
    /**
     * forClass function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function forClass($class)
    {
        return $this->getDefinitionForClass($class);
    }

    /**
     * getClasses function.
     * 
     * @access public
     * @return void
     */
    public function getClasses()
    {
        $classes = array();
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            $classes = array_merge($classes, $definition->getClasses());
        }
        return $classes;
    }
    
    /**
     * hasClass function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function hasClass($class)
    {
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * getClassSupertypes function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function getClassSupertypes($class)
    {
        $supertypes = array();
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            $supertypes = array_merge($supertypes, $definition->getClassSupertypes($class));
        }
        // @todo remove duplicates?
        return $supertypes;
    }

	/**
	 * classHasSupertype function.
	 * 
	 * @access public
	 * @param string $class
	 * @param string $supertype
	 * @return bool
	 */
	public function classHasSupertype($class, $supertype)
	{
		$supertypes = $this->getClassSupertypes($class);
		return in_array($supertype, $supertypes);
	}

    /**
     * getInstantiator function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function getInstantiator($class)
    {
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                $value = $definition->getInstantiator($class);
                if ($value === null && $definition instanceof Definition\PartialMarker) {
                    continue;
                } else {
                    return $value;
                }
            }
        }
        return false;
    }
    
    /**
     * hasMethods function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function hasMethods($class)
    {
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition->hasMethods($class) === false && $definition instanceof Definition\PartialMarker) {
                    continue;
                } else {
                    return $definition->hasMethods($class);
                }
            }
        }
        return false;
    }
    
    /**
     * hasMethod function.
     * 
     * @access public
     * @param mixed $class
     * @param mixed $method
     * @return void
     */
    public function hasMethod($class, $method)
    {
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition->hasMethods($class) === false && $definition instanceof Definition\PartialMarker) {
                    continue;
                } else {
                    return $definition->hasMethods($class);
                }
            }
        }
        return false;
    }
    
    /**
     * getMethods function.
     * 
     * @access public
     * @param mixed $class
     * @return void
     */
    public function getMethods($class)
    {
        /** @var $definition Definition\Definition */
        $methods = array();
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition instanceof Definition\PartialMarker) {
                    $methods = array_merge($definition->getMethods($class), $methods);
                } else {
                    return array_merge($definition->getMethods($class), $methods);
                }
            }
        }
        return $methods;
    }

    /**
     * hasMethodParameters function.
     * 
     * @access public
     * @param mixed $class
     * @param mixed $method
     * @return void
     */
    public function hasMethodParameters($class, $method)
    {
        $methodParameters = $this->getMethodParameters($class, $method);
        return ($methodParameters !== array());
    }

    /**
     * getMethodParameters function.
     * 
     * @access public
     * @param mixed $class
     * @param mixed $method
     * @return void
     */
    public function getMethodParameters($class, $method)
    {
        /** @var $definition Definition\Definition */
        foreach ($this as $definition) {
            if ($definition->hasClass($class) && $definition->hasMethod($class, $method) && $definition->hasMethodParameters($class, $method)) {
                return $definition->getMethodParameters($class, $method);
            }
        }
        return array();
    }
    
}