<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use ReflectionClass;
use ReflectionObject;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

use ZendTest\ServiceManager\TestAsset\FooPluginManager;
use ZendTest\ServiceManager\TestAsset\MockSelfReturningDelegatorFactory;

class AbstractPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager = null;

    public function setup()
    {
        $this->serviceManager = new ServiceManager;
        $this->pluginManager = new FooPluginManager(new Config([
            'factories' => [
                'Foo' => 'ZendTest\ServiceManager\TestAsset\FooFactory',
            ],
            'shared' => [
                'Foo' => false,
            ],
        ]));
    }

    public function testSetMultipleCreationOptions()
    {
        $pluginManager = new FooPluginManager(new Config([
            'factories' => [
                'Foo' => 'ZendTest\ServiceManager\TestAsset\FooFactory'
            ],
            'shared' => [
                'Foo' => false
            ]
        ]));

        $refl         = new ReflectionClass($pluginManager);
        $reflProperty = $refl->getProperty('factories');
        $reflProperty->setAccessible(true);

        $value = $reflProperty->getValue($pluginManager);
        $this->assertInternalType('string', $value['foo']);

        $pluginManager->get('Foo', ['key1' => 'value1']);

        $value = $reflProperty->getValue($pluginManager);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\FooFactory', $value['foo']);
        $this->assertEquals(['key1' => 'value1'], $value['foo']->getCreationOptions());

        $pluginManager->get('Foo', ['key2' => 'value2']);

        $value = $reflProperty->getValue($pluginManager);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\FooFactory', $value['foo']);
        $this->assertEquals(['key2' => 'value2'], $value['foo']->getCreationOptions());
    }

    /**
     * @group issue-4208
     */
    public function testGetFaultyRegisteredInvokableThrowsException()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');

        $pluginManager = new FooPluginManager();
        $pluginManager->setInvokableClass('helloWorld', 'IDoNotExist');
        $pluginManager->get('helloWorld');
    }

    public function testAbstractFactoryWithMutableCreationOptions()
    {
        $creationOptions = ['key1' => 'value1'];
        $mock = 'ZendTest\ServiceManager\TestAsset\AbstractFactoryWithMutableCreationOptions';
        $abstractFactory = $this->getMock($mock, ['setCreationOptions']);
        $abstractFactory->expects($this->once())
            ->method('setCreationOptions')
            ->with($creationOptions);

        $this->pluginManager->addAbstractFactory($abstractFactory);
        $instance = $this->pluginManager->get('classnoexists', $creationOptions);
        $this->assertTrue(is_object($instance));
    }

    public function testMutableMethodNeverCalledWithoutCreationOptions()
    {
        $mock = 'ZendTest\ServiceManager\TestAsset\CallableWithMutableCreationOptions';
        $callable = $this->getMock($mock, ['setCreationOptions']);
        $callable->expects($this->never())
            ->method('setCreationOptions');

        $ref = new ReflectionObject($this->pluginManager);

        $method = $ref->getMethod('createServiceViaCallback');
        $method->setAccessible(true);
        $method->invoke($this->pluginManager, $callable, 'foo', 'bar');
    }

    public function testCallableObjectWithMutableCreationOptions()
    {
        $creationOptions = ['key1' => 'value1'];
        $mock = 'ZendTest\ServiceManager\TestAsset\CallableWithMutableCreationOptions';
        $callable = $this->getMock($mock, ['setCreationOptions']);
        $callable->expects($this->once())
            ->method('setCreationOptions')
            ->with($creationOptions);

        $ref = new ReflectionObject($this->pluginManager);

        $property = $ref->getProperty('creationOptions');
        $property->setAccessible(true);
        $property->setValue($this->pluginManager, $creationOptions);

        $method = $ref->getMethod('createServiceViaCallback');
        $method->setAccessible(true);
        $method->invoke($this->pluginManager, $callable, 'foo', 'bar');
    }

    public function testValidatePluginIsCalledWithDelegatorFactoryIfItsAService()
    {
        $pluginManager = $this->getMockForAbstractClass('Zend\ServiceManager\AbstractPluginManager');
        $delegatorFactory = $this->getMock('Zend\\ServiceManager\\DelegatorFactoryInterface');

        $pluginManager->setService('delegator-factory', $delegatorFactory);
        $pluginManager->addDelegator('foo-service', 'delegator-factory');

        $pluginManager->expects($this->once())
            ->method('validatePlugin')
            ->with($delegatorFactory);

        $pluginManager->create('foo-service');
    }

    public function testSingleDelegatorUsage()
    {
        $delegatorFactory = $this->getMock('Zend\\ServiceManager\\DelegatorFactoryInterface');
        $pluginManager = $this->getMockForAbstractClass('Zend\ServiceManager\AbstractPluginManager');
        $realService = $this->getMock('stdClass', [], [], 'RealService');
        $delegator = $this->getMock('stdClass', [], [], 'Delegator');

        $delegatorFactory
            ->expects($this->once())
            ->method('createDelegatorWithName')
            ->with(
                $pluginManager,
                'fooservice',
                'foo-service',
                $this->callback(function ($callback) use ($realService) {
                    if (!is_callable($callback)) {
                        return false;
                    }

                    return call_user_func($callback) === $realService;
                })
            )
            ->will($this->returnValue($delegator));

        $pluginManager->setFactory('foo-service', function () use ($realService) {
            return $realService;
        });
        $pluginManager->addDelegator('foo-service', $delegatorFactory);

        $pluginManager->expects($this->once())
            ->method('validatePlugin')
            ->with($delegator);

        $this->assertSame($delegator, $pluginManager->get('foo-service'));
    }

    public function testMultipleDelegatorsUsage()
    {
        $pluginManager = $this->getMockForAbstractClass('Zend\ServiceManager\AbstractPluginManager');

        $fooDelegator = new MockSelfReturningDelegatorFactory();
        $barDelegator = new MockSelfReturningDelegatorFactory();

        $pluginManager->addDelegator('foo-service', $fooDelegator);
        $pluginManager->addDelegator('foo-service', $barDelegator);
        $pluginManager->setInvokableClass('foo-service', 'stdClass');

        $pluginManager->expects($this->once())
            ->method('validatePlugin')
            ->with($barDelegator);

        $this->assertSame($barDelegator, $pluginManager->get('foo-service'));
        $this->assertCount(1, $barDelegator->instances);
        $this->assertCount(1, $fooDelegator->instances);
        $this->assertInstanceOf('stdClass', array_shift($fooDelegator->instances));
        $this->assertSame($fooDelegator, array_shift($barDelegator->instances));
    }
}
