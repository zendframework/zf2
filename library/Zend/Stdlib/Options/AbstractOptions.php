<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Stdlib\Options;

use \Traversable,
	Zend\Stdlib\Options,
	Zend\Stdlib\Options\InvalidPropertyException,
	Zend\Stdlib\Exception\InvalidArgumentException;

/**
 * Base class for flexible component Configuration.
 *
 * To create Configuration class for your component, extend this class and provide public $properties and
 * setters/getters for all configurable parameters.
 *
 * This class features:
 *   - building Configuration from an array
 *   - building Configuration from a Traversable object (like Zend\Config or ArrayObject)
 *   - setting/getting any parameter via property: $config->parameter = 'value'
 *   - setting/getting any parameter via function call: $config->setParameter('value'), $config->getParameter()
 *   - serialization and unserialization
 *   - minimum inflection (when used in a accessor, only the first letter is lowercased)
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractOptions implements Options
{
	/**
	 * A cache of all public properties in a class
	 * 
	 * @var null|array
	 */
	protected static $_publicProperties = array();

	/**
	 * If this flag is set to false, attempts to access unknown properties will result in an exception.
	 * If this flag is set to true, attempts to access unknown properties will fail silently.
	 * 
	 * @var bool
	 */
	protected $_ignoreUnknownProperties = false;

	/**
	 * Constructs an instance of Configuration from an array of values.
	 *
	 * @throws Exception\InvalidArgumentException
	 * @param array|Traversable $config			An array or Traversable object to use for configuration.
	 * @param bool 				$ignoreUnknown	Silently ignore unknown properties
	 */
	public function __construct($config = array(), $ignoreUnknown = false)
	{
		$this->fromArray($config,$ignoreUnknown);
	}

	/**
	 * Update config parameters from values in an array.
	 *
	 * For each key in the array it will attempt to update the corresponding configuration values. This method accepts
	 * arrays and objects implementing Traversable interface, such as ArrayObject or Zend\Config.
	 *
	 * @throws Exception\InvalidArgumentException
	 * @param array|Traversable		$config			Array with config values.
	 * @param bool 					$ignoreUnknown	Silently ignore unrecognized array keys
	 */
	public function fromArray($config = array(), $ignoreUnknown = false)
	{
		if(
			!is_array($config) &&
			(
				!is_object($config) ||
				!($config instanceof Traversable)
			)
		){
			throw new InvalidArgumentException('Cannot use "'.gettype($config).'" for Configuration');
		}

		$ignoreBefore = $this->_ignoreUnknownProperties;
		$this->_ignoreUnknownProperties = $ignoreUnknown;

		foreach($config as $key=>$val){
			$this->$key = $val;
		}

		$this->_ignoreUnknownProperties = $ignoreBefore;	// restore flag
	}

	/**
	 * Returns an array holding all config parameters
	 *
	 * @return array
	 */
	public function toArray()
	{
		$result = array();

		$class = get_class($this);
		$reflection = new \ReflectionClass($class);

		/**
		 * Retrieve all public properties
		 */
		if(!isset(self::$_publicProperties[$class])){
			self::$_publicProperties[$class] = array();
			$reflection = new \ReflectionClass($class);
			foreach($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property){
				self::$_publicProperties[$class][$property->getName()] = true;
			}
		}

		/**
		 * Collect values from public properties
		 */
		foreach(array_keys(self::$_publicProperties[$class]) as $propertyName){
			$result[$propertyName] = $this->$propertyName;
		}

		/**
		 * Collect values from getters
		 */
		foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodName){
			if(strtolower(substr($methodName,0,3)) == 'get'){
				$propertyName = lcfirst(substr($methodName,3));
				$result[$propertyName] = call_user_func(array($this,$methodName));
			}
		}

		return $result;
	}

	/**
	 * Serializes this Configuration object to a string
	 *
	 * @return string			String with serialized array holding all config parameters.
	 */
	public function serialize()
	{
		return serialize($this->toArray());
	}

	/**
	 * Repopulates Configuration with parameters from a serialized string.
	 *
	 * @param sting $data		String with serialized array holding all config parameters.
	 * @return Options
	 */
	public function unserialize($data)
	{
		$this->fromArray(unserialize($data));
		return $this;
	}

	/**
	 * Generate setter function name for a given property.
	 *
	 * @param string $propertyName
	 * @return string
	 */
	protected function getSetterNameForProperty($propertyName)
	{
		return 'set'.ucfirst($propertyName);
	}

	/**
	 * Generate getter function name for a given property.
	 *
	 * @param string $propertyName
	 * @return string
	 */
	protected function getGetterNameForProperty($propertyName)
	{
		return 'get'.ucfirst($propertyName);
	}

	/**
	 * Retrieve access mode and public property name for a given accessor function name.
	 *
	 * Returns false if there is no such public property.
	 *
	 * @param string		$accessorName	Name of the accessor function
	 * @return bool|array					An array with access mode and property name, i.e. array('set','foo')
	 */
	protected function getPublicPropertyNameForAccessor($accessorName)
	{
		if(strlen($accessorName) < 4){
			// accessor name has to have at least 4 characters
			return false;
		}

		/**
		 * Extract access mode and property name from function name
		 *
		 *    $config->getParameter()          : get, parameter
		 *    $config->setParameter('value')   : set, parameter
		 */
		$accessMode = strtolower(substr($accessorName,0,3));
		$propertyName = lcfirst(substr($accessorName,3));

		/**
		 * Check access mode
		 */
		if($accessMode != 'set' && $accessMode != 'get'){
			// unknown access mode
			return false;
		}

		/**
		 * Retrieve all public properties (once for a class)
		 */
		$class = get_class($this);
		if(!isset(self::$_publicProperties[$class])){
			self::$_publicProperties[$class] = array();
			$reflection = new \ReflectionClass($class);
			foreach($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property){
				self::$_publicProperties[$class][$property->getName()] = true;
			}
		}

		/**
		 * Check if the property is publicly accessible
		 */
		if(isset(self::$_publicProperties[$class][$propertyName])){
			return array(
				$accessMode,
				$propertyName
			);
		}else{
			// property is not public
			return false;
		}
	}

	/**
	 * Handle setting protected parameters, which are accessed via a setter function.
	 *
	 * Setters usually perform additional validation and transformation of parameter's value.
	 *
	 * @throws Configuration\InvalidPropertyException
	 * @param $propertyName
	 * @param $value
	 * @return mixed
	 */
	public function __set($propertyName,$value)
	{
		$setter = $this->getSetterNameForProperty($propertyName);

		// Check if a setter function exists
		if(is_callable(array($this,$setter))){
			return call_user_func(
				array($this,$setter),
				$value
			);
		}
		elseif(!$this->_ignoreUnknownProperties){
			throw new InvalidPropertyException($propertyName);
		}
	}

	/**
	 * Handle retrieving protected parameters, which are accessed via a getter function.
	 *
	 * @throws Configuration\InvalidPropertyException
	 * @param $propertyName
	 * @return mixed
	 */
	public function __get($propertyName)
	{
		$setter = $this->getGetterNameForProperty($propertyName);

		// Check if a getter function exists
		if(is_callable(array($this,$setter))){
			return call_user_func(array($this,$setter));
		}
		elseif(!$this->_ignoreUnknownProperties){
			throw new InvalidPropertyException($propertyName);
		}
	}

	/**
	 * Handle explicit calls to setters and getters for accessing public properties.
	 *
	 * This allows for transparent usage of ->setParameter('value') for scalar values that do not have their setters
	 * (i.e. they do not require validation or transformation and exist as object's public properties)
	 *
	 * @throws Configuration\InvalidPropertyException
	 * @param string 	$accessorName
	 * @param array 	$values
	 * @return mixed
	 */
	public function __call($accessorName,$values)
	{
		// try to determine public property name from accessor
		$check = $this->getPublicPropertyNameForAccessor($accessorName);

		if($check === false){
			// there is no such public property
			if(!$this->_ignoreUnknownProperties){
				throw new InvalidPropertyException($accessorName);
			}else{
				return false;
			}
		}

		list($accessMode,$propertyName) = $check;
		if($accessMode == 'get'){
			return $this->$propertyName;
		}else{
			return $this->$propertyName = array_shift($values);
		}
	}
}