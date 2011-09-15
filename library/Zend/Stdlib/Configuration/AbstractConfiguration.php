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
namespace Zend\Stdlib;

use \Traversable,
	Zend\Stdlib\Configuration,
	Zend\Stdlib\Exception\InvalidArgumentException;

/**
 * Base class for flexible component Configuration. To create Configuration class for your component, extend
 * this class and provide public $properties and setters/getters for all configurable parameters.
 *
 * This class handles:
 *   - creation of Configuration from 
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractConfiguration implements Configuration
{
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
	public function fromArray($config = array(), $ignoreUnknown = false){
		if(is_array($config)){
			// Handle standard arrays
			foreach($config as $key=>$val){
				$this->__set($key,$val);
			}
		}elseif(is_object($config)){
			// Handle Traversable objects
			if($config instanceof Traversable){
				foreach($config as $key=>$val){
					$this->__set($key,$val);
				}
			}else{
				throw new InvalidArgumentException('Cannot use object of class '.get_class($config).' as config');
			}
		}else{
			throw new InvalidArgumentException('Cannot use "'.gettype($config).'" for Configuration');
		}
	}

	public function __set($what,$val)
	{
		$method = 'set'.ucfirst($what);
		if(is_callable(array($this,$method))){
			return call_user_func(
				array($this,$method),
				$val
			);
		}
		throw new \BadMethodCallException('Unknown config property '.$what);;
	}

	public function __get($what)
	{
		$method = 'set'.ucfirst($what);
		if(is_callable(array($this,$method))){
			return call_user_func(
				array($this,$method)
			);
		}
		throw new \BadMethodCallException('Unknown config property '.$what);;
	}

	public function __call($func,$array)
	{
		if(strlen($func) >= 5){			// handle getProperty()
			$prefix = strtolower(substr($func,0,3));
			$prop = strtolower(substr($func,4,1)).substr($func,5);
		}elseif(strlen($func) == 4){	// handle getA()
			$prefix = strtolower(substr($func,0,3));
			$prop = strtolower(substr($func,4,1));
		}

		if($prefix == 'set'){
			$refl = new \ReflectionClass(get_class($this));
			if()
		}elseif($prefix == 'get'){

		}


		if($refl->hasProperty())
		$method = 'set'.ucfirst($what);
		if(is_callable(array($this,$method))){
			return call_user_func(
				array($this,$method)
			);
		}
		throw new \BadMethodCallException('Unknown config property '.$what);;
	}
}


// using config array
$repeater = new Repeater(array(
	'count' => 1000,
	'text' => 'aBc'
));

// using Zend\Config
$config = new Zend\Config\Config(array(
	'count' => 500,
	'text' => 'foo '
));
$repeater = new Repeater($zconfig);

// using array with config object
$config = new RepeaterConfig(array(
	'count' => 500,
	'text' => 'foo '
));
$repeater = new Repeater($config);

// --------------------

// config using DI
$di = new Zend\Di\DependencyInjector;
$di->getInstanceManager()->setParameters('RepeaterConfig', array(array(
	'count' => 1,
	'text' => 'default text',
)));
$repeater = $di->newInstance('Repeater');


/*
 * If DI supported setters, config objects or sub-instances in the future, we
 * could probably use something like this:
 */
$di->getInstanceManager()->setParameters('RepeaterConfig', array(
	'count' => 1,
	'text' => 'default text',
	'filter' => new Zend\Di\InstanceParameter(
		'Zend\Filter\StripTags', // <- lazy load this class
		array(
			'commentsAllowed' => true
		)
	)
));




