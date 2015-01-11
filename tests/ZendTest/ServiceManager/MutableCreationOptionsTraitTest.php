<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use PHPUnit_Framework_TestCase as TestCase;

class MutableCreationOptionsTraitTest extends TestCase
{
    protected $stub;

    public function setUp()
    {
        $this->stub = $this->getObjectForTrait('Zend\ServiceManager\MutableCreationOptionsTrait');
    }

    public function tearDown()
    {
        unset($this->stub);
    }

    public function testCreationOptionsInitiallyIsArray()
    {
        $this->assertAttributeEquals(array(), 'creationOptions', $this->stub);
    }

    public function testTraitProvidesSetter()
    {
        $this->assertTrue(
            method_exists($this->stub, 'setCreationOptions')
        );
    }

    public function testTraitProvidesGetter()
    {
        $this->assertTrue(
            method_exists($this->stub, 'getCreationOptions')
        );
    }

    public function testTraitAcceptsCreationOptionsArray()
    {
        $creationOptions = array(
            'foo' => 'bar'
        );
        $this->stub->setCreationOptions($creationOptions);
        $this->assertEquals($creationOptions, $this->stub->getCreationOptions());
    }
}
