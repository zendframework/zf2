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
 * @package	Zend_Stdlib
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license	http://framework.zend.com/license/new-bsd	 New BSD License
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Configuration,
	Zend\Stdlib\Configuration\AbstractConfiguration,
	Zend\Stdlib\Configurable,
	ZendTest\Stdlib\Configuration\ComponentA,
	ZendTest\Stdlib\Configuration\ComponentAConfig

;

/**
 * @category   Zend
 * @package	Zend_Stdlib
 * @subpackage UnitTests
 * @group	  Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license	http://framework.zend.com/license/new-bsd	 New BSD License
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
	}

	public function testSimpleConfigDefaults(){
		$config = new ComponentAConfig();
		$this->assertTrue($config instanceof Configuration);
		$this->assertSame(null, $config->parameterA);
		$this->assertSame('defaultValue', $config->parameterB);
		$this->assertSame(15, $config->parameterC);
	}

	public function testSimpleConfigDefaultsViaAccessors(){
		$config = new ComponentAConfig();
		$this->assertTrue($config instanceof Configuration);
		$this->assertSame(null, $config->getParameterA());
		$this->assertSame('defaultValue', $config->getParameterB());
		$this->assertSame(15, $config->getParameterC());
	}

	public function testSimpleConfigSetters(){
		$config = new ComponentAConfig();
		$this->assertTrue($config instanceof Configuration);

		$value = mt_rand(1,PHP_INT_MAX);
		$config->setParameterA($value);
		$this->assertSame($value, $config->parameterA);
		$this->assertSame($value, $config->getParameterA());

		$this->assertSame('defaultValue', $config->getParameterB());
		$this->assertSame(15, $config->getParameterC());
	}

	public function testSimpleConfigToArray(){
		$config = new ComponentAConfig();
		
		$array = $config->toArray();
		$this->assertInternalType('array',$array);
		$this->assertNotEmpty($array);

		// check each value
		foreach(array(
			'parameterA',
			'parameterB',
			'parameterC'
		) as $param){
			$this->assertArrayHasKey($param,$array);
			$this->assertSame($config->$param, $array[$param]);
		}

	}

	public function testSimpleConfigFromArray(){
		$array = array(
			'parameterA' => mt_rand(1,PHP_INT_MAX),
			'parameterB' => uniqid(),
			'parameterC' => mt_rand(1,PHP_INT_MAX),
		);

		$config = new ComponentAConfig($array);

		// check each value
		foreach($array as $param => $value){
			$this->assertSame($value, $config->$param);
		}
	}

	public function testSimpleConfigFromArrayModification(){
		$config = new ComponentAConfig();

		// check defaults
		$this->assertSame(null, $config->parameterA);
		$this->assertSame('defaultValue', $config->parameterB);
		$this->assertSame(15, $config->parameterC);

		// modify 2 parameters
		$array = array(
			'parameterA' => mt_rand(1,PHP_INT_MAX),
			'parameterB' => uniqid(),
		);
		$config->fromArray($array);

		// check each value
		foreach($array as $param => $value){
			$this->assertSame($value, $config->$param);
		}

		// check the third parameter's default value
		$this->assertSame(15, $config->parameterC);
	}

	/**
	 * @expectedException     \Zend\Stdlib\Configuration\InvalidPropertyException
	 * @return void
	 */
	public function testUnknownPropertyAccessThrowsException(){
		$config = new ComponentAConfig();
		$config->parameterD = 15;
	}

	/**
	 * @expectedException     \Zend\Stdlib\Configuration\InvalidPropertyException
	 * @return void
	 */
	public function testUnknownPropertySetterThrowsException(){
		$config = new ComponentAConfig();
		$config->setParameterD(15);
	}

	/**
	 * @expectedException     \Zend\Stdlib\Configuration\InvalidPropertyException
	 * @return void
	 */
	public function testUnknownPropertyInConstructorThrowsException(){
		new ComponentAConfig(array(
			'parameterA' => mt_rand(1,PHP_INT_MAX),
			'parameterD' => 15
		));
	}

	/**
	 * @expectedException     \Zend\Stdlib\Configuration\InvalidPropertyException
	 * @return void
	 */
	public function testUnknownPropertyInFromArrayThrowsException(){
		$config = new ComponentAConfig();
		$config->fromArray(array(
			'parameterA' => mt_rand(1,PHP_INT_MAX),
			'parameterD' => 15
		));
	}

	public function testUnknownPropertyInConstructorWithIgnoreFlag(){
		$value = mt_rand(1,PHP_INT_MAX);
		$config = new ComponentAConfig(
			array(
				'parameterA' => $value,
				'parameterD' => 15
			),
			true	// ignore unknown properties
		);

		// check value of first parameter
		$this->assertSame($value, $config->parameterA);
	}

	public function testUnknownPropertyInFromArrayWithIgnoreFlag(){
		$value = mt_rand(1,PHP_INT_MAX);
		$config = new ComponentAConfig();
		$config->fromArray(
			array(
				'parameterA' => $value,
				'parameterD' => 15
			),
			true	// ignore unknown properties
		);

		// check value of first parameter
		$this->assertSame($value, $config->parameterA);
	}

	public function testSimpleConfigSerialization(){
		$config = new ComponentAConfig();

		// serialize config object
		$serialized = serialize($config);
		$this->assertInternalType('string',$serialized);
		$this->assertNotEmpty($serialized);
		$this->assertTrue(strlen($serialized) > 5);
		unset($config);

		// recreate config object from serialized string
		$config = unserialize($serialized);

		$this->assertInstanceOf('ZendTest\Stdlib\Configuration\ComponentAConfig',$config);
		$this->assertTrue($config instanceof Configuration);

		// check values via public properties
		$this->assertSame(null, $config->parameterA);
		$this->assertSame('defaultValue', $config->parameterB);
		$this->assertSame(15, $config->parameterC);

		// check values via accessors
		$this->assertSame(null, $config->getParameterA());
		$this->assertSame('defaultValue', $config->getParameterB());
		$this->assertSame(15, $config->getParameterC());

	}


}
