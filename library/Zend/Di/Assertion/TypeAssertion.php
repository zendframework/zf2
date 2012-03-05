<?php

namespace Zend\Di\Assertion;
use Zend\Di\Assertion,
	Zend\Di\Definition\Definition;

class TypeAssertion implements Assertion
{
	/**
	 * type
	 * 
	 * @var string
	 * @access protected
	 */
	protected $type;
	
	/**
	 * definition
	 * 
	 * @var Definition
	 * @access protected
	 */
	protected $definition;
	
	/**
	 * className
	 * The name of the class
	 * 
	 * @var mixed
	 * @access private
	 */
	private $className;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $type. (default: null)
	 * @param mixed Definition $definition. (default: null)
	 * @return void
	 */
	public function __construct($type = null, Definition $definition = null)
	{
		if ($type !== null) {
			$this->setType($type);
		}

		if ($definition instanceof Definition) {
			$this->setDefinition($definition);
		}
	}
	
	/**
	 * setType function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}
	
	/**
	 * getType function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * setDefinition function.
	 * 
	 * @access public
	 * @param mixed Definition $definition
	 * @return void
	 */
	public function setDefinition(Definition $definition)
	{
		$this->definition = $definition;
	}
	
	/**
	 * getDefinition function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getDefinition()
	{
		if ($this->definition === null) {
			$this->definition = new Definition\RuntimeDefinition();
		}
		return $this->definition;
	}
	
	/**
	 * hasDefinition function.
	 * 
	 * @access public
	 * @return void
	 */
	public function hasDefinition()
	{
		return ($this->definition instanceof Definition);
	}

	/**
	 * assert function.
	 * 
	 * @access public
	 * @param mixed $class
	 * @param mixed Definition $definition. (default: null)
	 * @return void
	 */
	public function assert($class, Definition $definition = null)
	{
		if (is_object($class)){
			$this->className = get_class($class);
		} else if (is_string($class)) {
			$this->className = $class;
		}
		
		// The Di instance will provide the definition if one is not
		// already set.
		if (!$this->hasDefinition() && $definition instanceof Definition) {
			$this->setDefinition($definition);
		}

		$finalDefinition = $this->getDefinition();
		
		return ($this->className == $this->type) || 
			$finalDefinition->classHasSupertype($this->className, $this->type);
	}
	
	/**
	 * __toString function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __toString()
	{
		return sprintf(
			'Type assertion failed for "%s" while attempting to retrieve "%s"',
			$this->type,
			$this->className
		);
	}
}