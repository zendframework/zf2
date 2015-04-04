<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\Factory;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\ServiceManager\Asset\InvokableObject;

class InvokableFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);
        $factory        = new InvokableFactory();

        $object = $factory($serviceLocator, InvokableObject::class, ['foo' => 'bar']);

        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals(['foo' => 'bar'], $object->options);
    }
}