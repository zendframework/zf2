<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Di\Definition;

use Zend\Di\Definition\ClassDefinition;
use Zend\Di\Di;
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
        $definition->setSupertypes(['superFoo']);
        $this->assertEquals([], $definition->getClassSupertypes('Bar'));
        $this->assertEquals(['superFoo'], $definition->getClassSupertypes('Foo'));
    }

    public function testGetInstantiator()
    {
        $definition = new ClassDefinition('Foo');
        $definition->setInstantiator('__construct');
        $this->assertNull($definition->getInstantiator('Bar'));
        $this->assertEquals('__construct', $definition->getInstantiator('Foo'));
    }

    public function testGetMethods()
    {
        $definition = new ClassDefinition('Foo');
        $definition->addMethod("setVar", true);
        $this->assertEquals([], $definition->getMethods('Bar'));
        $this->assertEquals(['setVar' => true], $definition->getMethods('Foo'));
    }

    public function testHasMethod()
    {
        $definition = new ClassDefinition('Foo');
        $definition->addMethod("setVar", true);
        $this->assertNull($definition->hasMethod('Bar', "setVar"));
        $this->assertTrue($definition->hasMethod('Foo', "setVar"));
    }

    public function testHasMethodParameters()
    {
        $definition = new ClassDefinition('Foo');
        $definition->addMethodParameter("setVar", "var", [null, true]);
        $this->assertFalse($definition->hasMethodParameters("Bar", "setVar"));
        $this->assertTrue($definition->hasMethodParameters("Foo", "setVar"));
    }

    public function testGetMethodParameters()
    {
        $definition = new ClassDefinition('Foo');
        $definition->addMethodParameter("setVar", "var", ['type' => null, 'required' => true, 'default' => 'test']);
        $this->assertNull($definition->getMethodParameters("Bar", "setVar"));
        $this->assertEquals(
            ['Foo::setVar:var' => ["var", null, true, 'test']],
            $definition->getMethodParameters("Foo", "setVar")
        );
    }

    public function testAddMethodSetsCorrectConstructorType()
    {
        $definition = new ClassDefinition('Foo');
        $definition->addMethod('__construct');
        $this->assertEquals(['__construct' => Di::METHOD_IS_CONSTRUCTOR], $definition->getMethods('Foo'));
    }
}
