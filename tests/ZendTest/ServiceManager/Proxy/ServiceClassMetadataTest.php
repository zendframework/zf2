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

/**
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceClassMetadataTest extends PHPUnit_Framework_TestCase
{
    public function testClassMetadataFromString()
    {
        $classMetadata = new ServiceClassMetadata('stdClass');
        $this->assertSame('stdClass', $classMetadata->getName());
        $this->assertInstanceOf('ReflectionClass', $classMetadata->getReflectionClass());
    }

    public function testClassMetadataFromObject()
    {
        $classMetadata = new ServiceClassMetadata(new \stdClass());
        $this->assertSame('stdClass', $classMetadata->getName());
        $this->assertInstanceOf('ReflectionClass', $classMetadata->getReflectionClass());
    }
}
