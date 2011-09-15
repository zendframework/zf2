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

	}

	public function testSimpleConfigSerialization(){
		$config = new ComponentAConfig();

		// serialize config object
		$serialized = $config->serialize();
		$this->assertInternalType('string',$serialized);
		$this->assertNotEmpty($serialized);
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
