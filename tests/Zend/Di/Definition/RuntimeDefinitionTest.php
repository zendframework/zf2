<?php

namespace ZendTest\Di\Definition;

use Zend\Di\Definition\RuntimeDefinition,
    PHPUnit_Framework_TestCase as TestCase;

class RuntimeDefinitionTest extends TestCase
{
    public function testStub()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetInstantiatorForFullQualifiedNamespace(){
        $RuntimeDefinition = new RuntimeDefinition();
        $instantiator = $RuntimeDefinition->getInstantiator('\ZendTest\Di\TestAsset\BasicClass');
        $this->assertEquals($instantiator, '__construct');
    }
    
    public function testSupertypes(){
        $RuntimeDefinition = new RuntimeDefinition();
        $supertypes = $RuntimeDefinition->getClassSupertypes('ZendTest\Di\TestAsset\PreferredImplClasses\EofB');
        $this->assertEquals($supertypes, array(
            'ZendTest\Di\TestAsset\PreferredImplClasses\BofA' => 'ZendTest\Di\TestAsset\PreferredImplClasses\BofA',
            'ZendTest\Di\TestAsset\PreferredImplClasses\A' => 'ZendTest\Di\TestAsset\PreferredImplClasses\A'
            ));
    }
}
