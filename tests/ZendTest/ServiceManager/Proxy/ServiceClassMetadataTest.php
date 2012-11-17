<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\Proxy;

use Zend\ServiceManager\Proxy\ServiceClassMetadata;
use PHPUnit_Framework_TestCase;

use ZendTest\ServiceManager\TestAsset\PublicPropertiesLazyService;

/**
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceClassMetadataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::__construct
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getReflectionClass
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getName
     */
    public function testClassMetadataFromString()
    {
        $classMetadata = new ServiceClassMetadata('stdClass');
        $this->assertSame('stdClass', $classMetadata->getName());
        $this->assertInstanceOf('ReflectionClass', $classMetadata->getReflectionClass());
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::__construct
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getReflectionClass
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getName
     */
    public function testClassMetadataFromObject()
    {
        $classMetadata = new ServiceClassMetadata(new \stdClass());
        $this->assertSame('stdClass', $classMetadata->getName());
        $this->assertInstanceOf('ReflectionClass', $classMetadata->getReflectionClass());
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getReflectionClass
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getName
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::getFieldNames
     * @covers \Zend\ServiceManager\Proxy\ServiceClassMetadata::hasField
     */
    public function testGetFields()
    {
        $classMetadata = new ServiceClassMetadata(new PublicPropertiesLazyService());
        $this->assertSame('ZendTest\ServiceManager\TestAsset\PublicPropertiesLazyService', $classMetadata->getName());
        $this->assertInstanceOf('ReflectionClass', $classMetadata->getReflectionClass());
        $this->assertSame(array('checkedProperty'), $classMetadata->getFieldNames());
        $this->assertTrue($classMetadata->hasField('checkedProperty'));
        $this->assertFalse($classMetadata->hasField('non_existing'));
    }
}
