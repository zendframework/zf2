<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Di\Definition;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Di\Definition\RuntimeDefinition;

class RuntimeDefinitionTest extends TestCase
{
    /**
     * @group ZF2-308
     */
    public function testStaticMethodsNotIncludedInDefinitions()
    {
        $definition = new RuntimeDefinition;
        $this->assertTrue($definition->hasMethod('ZendTest\Di\TestAsset\SetterInjection\StaticSetter', 'setFoo'));
        $this->assertFalse($definition->hasMethod('ZendTest\Di\TestAsset\SetterInjection\StaticSetter', 'setName'));
    }

    public function testIncludesDefaultMethodParameters()
    {
        $definition = new RuntimeDefinition();

        $definition->forceLoadClass('ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters');

        $this->assertSame(
            [
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:0' => [
                    'a',
                    null,
                    false,
                    null,
                ],
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:1' => [
                    'b',
                    null,
                    false,
                    'defaultConstruct',
                ],
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:2' => [
                    'c',
                    null,
                    false,
                    [],
                ],
            ],
            $definition->getMethodParameters(
                'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters',
                '__construct'
            )
        );
    }

    public function testExceptionDefaultValue()
    {
        $definition = new RuntimeDefinition();

        $definition->forceLoadClass('RecursiveIteratorIterator');

        $this->assertSame(
            [
                'RecursiveIteratorIterator::__construct:0' => [
                    'iterator',
                    'Traversable',
                    true,
                    null,
                ],
                'RecursiveIteratorIterator::__construct:1' => [
                    'mode',
                    null,
                    true,
                    null,
                ],
                'RecursiveIteratorIterator::__construct:2' => [
                    'flags',
                    null,
                    true,
                    null,
                ],
            ],
            $definition->getMethodParameters(
                'RecursiveIteratorIterator',
                '__construct'
            )
        );
    }

    /**
     * Test if methods from aware interfaces without params are excluded
     */
    public function testExcludeAwareMethodsWithoutParameters()
    {
        $definition = new RuntimeDefinition();
        $this->assertTrue($definition->hasMethod('ZendTest\Di\TestAsset\AwareClasses\B', 'setSomething'));
        $this->assertFalse($definition->hasMethod('ZendTest\Di\TestAsset\AwareClasses\B', 'getSomething'));
    }

    /**
     * Test to see if we can introspect explicit classes
     */
    public function testExplicitClassesStillGetProccessedByIntrospectionStrategy()
    {
        $className = 'ZendTest\Di\TestAsset\ConstructorInjection\OptionalParameters';
        $explicitClasses = [$className => true];
        $definition = new RuntimeDefinition(null, $explicitClasses);

        $this->assertTrue($definition->hasClass($className));
        $this->assertSame(["__construct"=> 3], $definition->getMethods($className));
    }
}
