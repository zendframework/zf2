<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */
namespace ZendTest\Di\Definition;

use Zend\Di\Definition\ClassDefinition;
use PHPUnit_Framework_TestCase as TestCase;

class ClassDefinitionTest extends TestCase
{
    public function testClassImplementsDefinition()
    {
        $definition = new ClassDefinition('Foo');
        $this->assertInstanceOf('Zend\Di\Definition\DefinitionInterface', $definition);
    }

    public function testClassDefinitionHasMethods()
    {
        $definition = new ClassDefinition('Foo');
        $this->assertFalse($definition->hasMethods('Foo'));
        $definition->addMethod('doBar');
        $this->assertTrue($definition->hasMethods('Foo'));
    }
    
    public function testGetClassSupertypes()
    {
        $definition = new ClassDefinition('Foo');
        $definition->setSupertypes(array('superFoo'));
        $this->assertEquals(array(), $definition->getClassSupertypes('Bar'));
        $this->assertEquals(array('superFoo'), $definition->getClassSupertypes('Foo'));
    }
    
    public function testGetInstantiator()
    {
        $definition = new ClassDefinition('Foo');
        $definition->setInstantiator('__construct');
        $this->assertNull($definition->getInstantiator('Bar'));
        $this->assertEquals('__construct', $definition->getInstantiator('Foo'));
    }
}
