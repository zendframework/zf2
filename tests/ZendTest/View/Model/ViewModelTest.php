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

use ArrayObject;
use stdClass;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Variables;
use ZendTest\View\Model\TestAsset\Variable;

class ViewModelTest extends TestCase
{
    public function testParentClassIsAbstractModel()
    {
        $model = new ViewModel();
        $this->assertSame('Zend\View\Model\AbstractModel', get_parent_class($model));
    }

    public function testDefaultObjectState()
    {
        $model = new ViewModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\ViewModelOptions', $model->getOptions());
        $this->assertInstanceOf('ArrayIterator', $model->getIterator());
        $this->assertEquals(array(), $model->getChildren());
    }

    public function testAllowsEmptyOptionsArgumentToConstructor()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\ViewModelOptions', $model->getOptions());
    }

    public function testAllowsPassingBothVariablesAndOptionsArgumentsToConstructor()
    {
        $model = new ViewModel(array('foo' => 'bar'), array('template' => 'foo/bar'));
        $this->assertEquals(array('foo' => 'bar'), $model->getVariables());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingTraversableArgumentsToVariablesAndOptionsInConstructor()
    {
        $vars    = new ArrayObject;
        $options = new ArrayObject;
        $model = new ViewModel($vars, $options);
        $this->assertSame($vars, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\ViewModelOptions', $model->getOptions());
    }

    public function testAllowsPassingNonArrayAccessObjectsAsArrayInConstructor()
    {
        $vars  = array('foo' => new Variable);
        $model = new ViewModel($vars);
        $this->assertSame($vars, $model->getVariables());
    }

    public function testPassingAnInvalidArgumentToSetVariablesRaisesAnException()
    {
        $model = new ViewModel();
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects an array');
        $model->setVariables(new stdClass);
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $model = new ViewModel();
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Model\ViewModelOptions; received "stdClass"');
        $model->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfViewModelOptions()
    {
        $model = new ViewModel();

        $this->assertInstanceOf(
            'Zend\View\Model\ViewModelOptions',
            $model->getOptions());
    }

    public function testOptionsAreInternallyConvertedToAnArrayFromTraversables()
    {
        $options = new ArrayObject(array('terminal' => true));
        $model = new ViewModel();
        $model->setOptions($options);
        $this->assertInstanceOf('Zend\View\Model\ViewModelOptions', $model->getOptions());
        $this->assertTrue($model->getOptions()->isTerminal());
    }

    public function testAllowsPassingViewVariablesContainerAsVariablesToConstructor()
    {
        $variables = new Variables();
        $model     = new ViewModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testPassingOverwriteFlagWhenSettingVariablesOverwritesContainer()
    {
        $variables = new Variables(array('foo' => 'bar'));
        $model     = new ViewModel($variables);
        $overwrite = new Variables(array('foo' => 'baz'));
        $model->setVariables($overwrite, true);
        $this->assertSame($overwrite, $model->getVariables());
    }
}
