<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Password\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Crypt\Password\Algorithm\Factory\BcryptFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Zend\Crypt\Password\Algorithm\Factory\BcryptFactory
 */
class BcryptFactoryTest extends TestCase
{
    public function testFactoryFromServiceLocator()
    {
        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Config', array());

        $factory = new BcryptFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\Algorithm\Bcrypt', $factory->createService($locator));
    }

    public function testFactoryFromPluginManager()
    {
        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Config', array());

        $pluginManager = $this->getMock('Zend\ServiceManager\AbstractPluginManager');
        $pluginManager->expects($this->once())
                      ->method('getServiceLocator')
                      ->will($this->returnValue($locator));

        $factory = new BcryptFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\Algorithm\Bcrypt', $factory->createService($pluginManager));
    }

    public function testFactoryWithCostSet()
    {
        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Config', array('password' => array('bcrypt' => array('cost' => 4))));

        $factory = new BcryptFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\Algorithm\Bcrypt', $factory->createService($locator));
    }
}
