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
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\View\Model\ConsoleModel;
use Zend\View\Model\ConsoleModelOptions;
use Zend\View\Variables;
use ZendTest\View\Model\TestAsset\Variable;

class ConsoleModelTest extends TestCase
{
    public function testDefaultObjectState()
    {
        $model = new ConsoleModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\ConsoleModelOptions', $model->getOptions());
        $this->assertEquals(array(), $model->getChildren());
    }

    public function testAllowsPassingVariablesAsViewVariablesInConstructor()
    {
        $variables = new Variables();

        $model  = new ConsoleModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testAllowsPassingOptionsAsConsoleModelOptionsInConstructor()
    {
        $options = new ConsoleModelOptions();

        $model = new ConsoleModel(array(), $options);
        $this->assertSame($options, $model->getOptions());
    }

    public function testAllowsPassingVariablesAsArrayInConstructor()
    {
        $variables = array('foo' => new Variable);

        $model = new ConsoleModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testAllowsPassingOptionsAsArrayInConstructor()
    {
        $options = array('template' => 'foo/bar');

        $model = new ConsoleModel(array(), $options);
        $this->assertInstanceOf('Zend\View\Model\ConsoleModelOptions', $model->getOptions());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingBothVariablesAndOptionsArgumentsInConstructor()
    {
        $variables  = array('foo' => 'bar');
        $options    = array('template' => 'foo/bar');

        $model = new ConsoleModel($variables, $options);
        $this->assertEquals($variables, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\ConsoleModelOptions', $model->getOptions());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingTraversableArgumentsToVariablesAndOptionsInConstructor()
    {
        $variables = new ArrayObject(array('foo' => 'bar'));
        $options   = new ArrayObject(array('terminal' => false));

        $model = new ConsoleModel($variables, $options);
        $this->assertSame($variables, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\ConsoleModelOptions', $model->getOptions());
        $this->assertFalse($model->getOptions()->isTerminal());
    }

    public function testPassingAnInvalidVariablesRaisesAnException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects an array');

        $model = new ConsoleModel();
        $model->setVariables(new stdClass);
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Model\ConsoleModelOptions; received "stdClass"');

        $model = new ConsoleModel();
        $model->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfConsoleModelOptions()
    {
        $model = new ConsoleModel();

        $this->assertInstanceOf(
            'Zend\View\Model\ConsoleModelOptions',
            $model->getOptions());
    }

    public function testPassingOverwriteFlagWhenSettingVariablesOverwritesContainer()
    {
        $variables = new Variables(array('foo' => 'bar'));
        $model     = new ConsoleModel($variables);
        $overwrite = new Variables(array('foo' => 'baz'));
        $model->setVariables($overwrite, true);
        $this->assertSame($overwrite, $model->getVariables());
    }

    public function testResultIsVariable()
    {
        $model = new ConsoleModel();
        $model->setResult('foo');
        $this->assertEquals('foo', $model->getVariable(ConsoleModel::RESULT));
    }

    public function testResultIsMutable()
    {
        $model = new ConsoleModel();
        $model->setResult('foo');
        $this->assertEquals('foo', $model->getResult());
    }
}
