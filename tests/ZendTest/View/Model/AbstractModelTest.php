<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Model;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Model\AbstractModel;
use Zend\View\Model\ViewModel;
use Zend\View\Variables;

class AbstractModelTest extends TestCase
{
    /**
     * @var AbstractModel
     */
    protected $model;

    protected function setUp()
    {
        $this->model = $this->getMockForAbstractClass('Zend\View\Model\AbstractModel');
    }

    public function testImplementsModelInterface()
    {
        $this->assertInstanceOf('Zend\View\Model\ModelInterface', $this->model);
    }

    public function testImplementsClearableModelInterface()
    {
        $this->assertInstanceOf('Zend\View\Model\ClearableModelInterface', $this->model);
    }

    public function testCanSetVariablesSingly()
    {
        $this->model->setVariables(array('foo' => 'bar'));
        $this->model->setVariable('bar', 'baz');

        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $this->model->getVariables());
    }

    public function testCanOverwriteVariablesSingly()
    {
        $this->model->setVariables(array('foo' => 'bar'));
        $this->model->setVariable('foo', 'baz');

        $this->assertEquals(array('foo' => 'baz'), $this->model->getVariables());
    }

    public function testSetVariablesMergesWithPreviouslyStoredVariables()
    {
        $this->model->setVariables(array('foo' => 'bar', 'bar' => 'baz'));
        $this->model->setVariables(array('bar' => 'BAZBAT'));

        $this->assertEquals(array('foo' => 'bar', 'bar' => 'BAZBAT'), $this->model->getVariables());
    }

    public function testCanSetVariablesWithClass()
    {
        $this->model->setVariables(new Variables(array('foo' => 'bar')));

        $this->assertEquals(array('foo' => 'bar'), $this->model->getVariables());
    }

    public function testCanGetVariablesSingly()
    {
        $this->model->setVariables(array('foo' => 'bar'));

        $this->assertEquals('bar', $this->model->foo);
        $this->assertEquals('bar', $this->model->getVariable('foo'));
    }

    public function testPropertyOverloadingAllowsWritingPropertiesAfterSetVariablesHasBeenCalled()
    {
        $this->model->setVariables(array('foo' => 'bar'));
        $this->model->bar = 'baz';

        $this->assertTrue(isset($this->model->bar));
        $this->assertEquals('baz', $this->model->bar);
        $variables = $this->model->getVariables();
        $this->assertTrue(isset($variables['bar']));
        $this->assertEquals('baz', $variables['bar']);
    }

    public function testCanUnsetVariable()
    {
        $this->model->setVariables(array('foo' => 'bar'));
        $this->model->__unset('foo');

        $this->assertEquals(array(), $this->model->getVariables());
    }

    public function testCanClearAllVariables()
    {
        $this->model->clearVariables();

        $this->assertEquals(0, count($this->model->getVariables()));
    }

    public function testHasNoChildrenByDefault()
    {
        $this->assertFalse($this->model->hasChildren());
    }

    public function testWhenNoChildrenCountIsZero()
    {
        $this->assertEquals(0, count($this->model));
    }

    public function testCanAddChildren()
    {
        $child = new ViewModel();
        $this->model->addChild($child);

        $this->assertTrue($this->model->hasChildren());
    }

    public function testCanCountChildren()
    {
        $child = new ViewModel();
        $this->model->addChild($child);
        $this->assertEquals(1, count($this->model));
        $this->model->addChild($child);
        $this->assertEquals(2, count($this->model));
    }

    public function testCanIterateChildren()
    {
        $child = new ViewModel();
        $this->model->addChild($child);
        $this->model->addChild($child);
        $this->model->addChild($child);

        $count = 0;
        foreach ($this->model as $childModel) {
            $this->assertSame($child, $childModel);
            $count++;
        }
        $this->assertEquals(3, $count);
    }

    public function testCanClearChildren()
    {
        $this->model->clearChildren();
        $this->assertEquals(0, count($this->model));
    }

    public function testAddChildAllowsSpecifyingCaptureToValue()
    {
        $child = new ViewModel();
        $this->model->addChild($child, 'foo');
        $this->assertTrue($this->model->hasChildren());
        $this->assertEquals('foo', $child->getOptions()->captureTo());
    }
}
