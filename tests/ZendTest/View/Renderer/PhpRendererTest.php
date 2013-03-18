<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Renderer;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Variables;

class PhpRendererTest extends TestCase
{
    /**
     * @var PhpRenderer
     */
    protected $renderer;

    public function setUp()
    {
        $this->renderer = new PhpRenderer();
    }

    public function testEngineIsInstanceOfPhpRenderer()
    {
        $this->assertInstanceOf('Zend\View\Renderer\PhpRenderer', $this->renderer->getEngine());
    }

    public function testFilterChainReturnInstanceOfFilterChain()
    {
        $this->assertInstanceOf(
            'Zend\Filter\FilterChain',
            $this->renderer->getFilterChain());
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Renderer\PhpRendererOptions; received "stdClass"');

        $this->renderer->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfPhpRendererOptions()
    {
        $this->assertInstanceOf(
            'Zend\View\Renderer\PhpRendererOptions',
            $this->renderer->getOptions());
    }

    public function testPluginReturnInstanceOfPlugin()
    {
        $this->assertInstanceOf('Zend\View\Helper\Layout', $this->renderer->plugin('layout'));
        $this->assertInstanceOf('Zend\View\Helper\ViewModel', $this->renderer->plugin('viewModel'));
    }

    public function testPluginManagerReturnInstanceOfPluginManager()
    {
        $this->assertInstanceOf('Zend\View\HelperPluginManager', $this->renderer->getHelperPluginManager());
    }

    public function testVarsSetterRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected array or ArrayAccess object; received "stdClass"');

        $this->renderer->setVars(new stdClass);
    }

    public function testVarsSetterIsMutable()
    {
        $this->renderer->setVars(array('foo' => 'bar'));
        $this->assertEquals('bar', $this->renderer->get('foo'));

        $this->renderer->setVars(array('foo' => 'baz'));
        $this->assertEquals('baz', $this->renderer->get('foo'));
    }

    public function testVarsSetterAcceptArray()
    {
        $this->renderer->setVars(array('foo' => 'bar'));

        $this->assertInstanceOf('Zend\View\Variables', $this->renderer->vars());
        $this->assertEquals(array('foo' => 'bar'), $this->renderer->vars()->getArrayCopy());
    }

    public function testVarsSetterAcceptArrayAccessClass()
    {
        $this->renderer->setVars(new ArrayObject(array('foo' => 'bar')));

        $this->assertInstanceOf('Zend\View\Variables', $this->renderer->vars());
        $this->assertEquals(array('foo' => 'bar'), $this->renderer->vars()->getArrayCopy());
    }

    public function testVarsSetterAcceptVariablesClass()
    {
        $this->renderer->setVars(new Variables(array('foo' => 'bar')));

        $this->assertInstanceOf('Zend\View\Variables', $this->renderer->vars());
        $this->assertEquals(array('foo' => 'bar'), $this->renderer->vars()->getArrayCopy());
    }

    public function testVarsCanBeSetByPropery()
    {
        $this->renderer->foo = 'bar';

        $this->assertEquals('bar', $this->renderer->vars('foo'));
        $this->assertEquals(array('foo' => 'bar'), $this->renderer->vars()->getArrayCopy());
    }

    public function testVarsCanBeGetByPropery()
    {
        $this->renderer->setVars(array('foo' => 'bar'));

        $this->assertEquals('bar', $this->renderer->foo);
    }

    public function testVarsCanBeChangedByPropery()
    {
        $this->renderer->setVars(array('foo' => 'bar'));
        $this->renderer->foo = 'baz';

        $this->assertEquals(array('foo' => 'baz'), $this->renderer->vars()->getArrayCopy());
    }

    public function testVarsCanBeUnset()
    {
        $this->renderer->setVars(array('foo' => 'bar'));
        $this->renderer->__unset('foo');

        $this->assertEquals(array(), $this->renderer->vars()->getArrayCopy());
    }
}
