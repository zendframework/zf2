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
use Zend\Mvc\Service\CryptHandlerAggregateFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Zend\Crypt\Password\Factory\HandlerAggregateFactory
 */
class HandlerAggregateFactoryTest extends TestCase
{
    public function testFactoryWithoutConfig()
    {
        $handlerManager = $this->getMock('Zend\Crypt\Password\HandlerManager');

        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Password\HandlerManager', $handlerManager);
        $locator->setService('Zend\Crypt\Config', array());

        $factory = new CryptHandlerAggregateFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\HandlerAggregate', $factory->createService($locator));
    }

    public function testFactoryWithConfig()
    {
        $handlerManager = $this->getMock('Zend\Crypt\Password\HandlerManager');

        $locator = new ServiceManager();
        $locator->setService('Zend\Crypt\Password\HandlerManager', $handlerManager);
        $locator->setService('Zend\Crypt\Config', array('password' => array('handler_aggregate' => array())));

        $factory = new CryptHandlerAggregateFactory();
        $this->assertInstanceOf('Zend\Crypt\Password\HandlerAggregate', $factory->createService($locator));
    }
}
