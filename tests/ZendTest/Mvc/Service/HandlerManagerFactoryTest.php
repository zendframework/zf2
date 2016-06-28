<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\CryptHandlerManagerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Zend\Crypt\Password\Factory\HandlerManagerFactory
 */
class HandlerManagerFactoryTest extends TestCase
{
    public function testFactoryWithoutConfig()
    {
        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Config', array());

        $factory = new CryptHandlerManagerFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\HandlerManager', $factory->createService($locator));
    }

    public function testFactoryWithConfig()
    {
        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Config', array('password' => array('handler_manager' => array())));

        $factory = new CryptHandlerManagerFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\HandlerManager', $factory->createService($locator));
    }
}
