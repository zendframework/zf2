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
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Zend\View\Model\JsonModelOptions;
use Zend\View\Variables;
use ZendTest\View\Model\TestAsset\Variable;

class JsonModelTest extends TestCase
{
    public function testDefaultObjectState()
    {
        $model = new JsonModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\JsonModelOptions', $model->getOptions());
        $this->assertInstanceOf('ArrayIterator', $model->getIterator());
        $this->assertEquals(array(), $model->getChildren());
    }

    public function testAllowsPassingVariablesAsViewVariablesInConstructor()
    {
        $variables = new Variables();

        $model  = new JsonModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testAllowsPassingOptionsAsJsonModelOptionsInConstructor()
    {
        $options = new JsonModelOptions();

        $model = new JsonModel(array(), $options);
        $this->assertSame($options, $model->getOptions());
    }

    public function testAllowsPassingVariablesAsArrayInConstructor()
    {
        $variables = array('foo' => new Variable);

        $model = new JsonModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testAllowsPassingOptionsAsArrayInConstructor()
    {
        $options = array('template' => 'foo/bar');

        $model = new JsonModel(array(), $options);
        $this->assertInstanceOf('Zend\View\Model\JsonModelOptions', $model->getOptions());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingBothVariablesAndOptionsArgumentsInConstructor()
    {
        $variables  = array('foo' => 'bar');
        $options    = array('template' => 'foo/bar');

        $model = new JsonModel($variables, $options);
        $this->assertEquals($variables, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\JsonModelOptions', $model->getOptions());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingTraversableArgumentsToVariablesAndOptionsInConstructor()
    {
        $variables = new ArrayObject(array('foo' => 'bar'));
        $options   = new ArrayObject(array('terminal' => false));

        $model = new JsonModel($variables, $options);
        $this->assertSame($variables, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\JsonModelOptions', $model->getOptions());
        $this->assertFalse($model->getOptions()->isTerminal());
    }

    public function testPassingAnInvalidVariablesRaisesAnException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects an array');

        $model = new JsonModel();
        $model->setVariables(new stdClass);
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Model\JsonModelOptions; received "stdClass"');

        $model = new JsonModel();
        $model->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfJsonModelOptions()
    {
        $model = new JsonModel();

        $this->assertInstanceOf(
            'Zend\View\Model\JsonModelOptions',
            $model->getOptions());
    }

    public function testPassingOverwriteFlagWhenSettingVariablesOverwritesContainer()
    {
        $variables = new Variables(array('foo' => 'bar'));
        $model     = new JsonModel($variables);
        $overwrite = new Variables(array('foo' => 'baz'));
        $model->setVariables($overwrite, true);
        $this->assertSame($overwrite, $model->getVariables());
    }

    public function testCanSerializeVariablesToJson()
    {
        $array = array('foo' => 'bar');
        $model = new JsonModel($array);
        $this->assertEquals($array, $model->getVariables());
        $this->assertEquals(Json::encode($array), $model->serialize());
    }


    public function testCanSerializeWithJsonpCallback()
    {
        $array = array('foo' => 'bar');
        $model = new JsonModel($array);
        $model->getOptions()->setJsonpCallback('callback');
        $this->assertEquals('callback(' . Json::encode($array) . ');', $model->serialize());
    }
}
