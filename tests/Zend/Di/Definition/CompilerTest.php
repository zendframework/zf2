<?php

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\Compiler,
    Zend\Code\Scanner\DirectoryScanner,
    PHPUnit_Framework_TestCase as TestCase;

class CompilerTest extends TestCase
{
    public function testCompilerCompilesAgainstConstructorInjectionAssets()
    {
        $compiler = new Compiler;
        $compiler->addCodeScannerDirectory(new DirectoryScanner(__DIR__ . '/../TestAsset/CompilerClasses'));
        $definition = $compiler->compile();
        $this->assertInstanceOf('Zend\Di\Definition\ArrayDefinition', $definition);
        
        $this->assertTrue($definition->hasClass('ZendTest\Di\TestAsset\CompilerClasses\A'));
        
        $assertClasses = array(
            'ZendTest\Di\TestAsset\CompilerClasses\A',
            'ZendTest\Di\TestAsset\CompilerClasses\B',
            'ZendTest\Di\TestAsset\CompilerClasses\C',
            'ZendTest\Di\TestAsset\CompilerClasses\D',
        );
        $classes = $definition->getClasses();
        foreach ($assertClasses as $assertClass) {
            $this->assertContains($assertClass, $classes);
        }

        // @todo this needs to be resolved, not the short name
        // $this->assertContains('ZendTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('ZendTest\Di\TestAsset\CompilerClasses\D'));
        
        $this->assertEquals('__construct', $definition->getInstantiator('ZendTest\Di\TestAsset\CompilerClasses\A'));
        $this->assertTrue($definition->hasInjectionMethods('ZendTest\Di\TestAsset\CompilerClasses\C'));
        
        
        $this->assertContains('setB', $definition->getInjectionMethods('ZendTest\Di\TestAsset\CompilerClasses\C'));
        $this->assertTrue($definition->hasInjectionMethod('ZendTest\Di\TestAsset\CompilerClasses\C', 'setB'));
        
        $params = array(
            'b' => array(
                'ZendTest\Di\TestAsset\CompilerClasses\B',
                false, // ZF2-46 
                true,
            ),
        );
        $this->assertEquals($params, $definition->getInjectionMethodParameters('ZendTest\Di\TestAsset\CompilerClasses\C', 'setB'));

        // I'm not sure there's a use-case for a setter with an optional 
        // parameter, but we should test that it reflects it accurately.
        $params = array(
            'a' => array(
                'ZendTest\Di\TestAsset\CompilerClasses\A',
                true,
                true,
            ),
        );
        $this->assertEquals($params, $definition->getInjectionMethodParameters('ZendTest\Di\TestAsset\CompilerClasses\C', 'setA'));
    }
}
