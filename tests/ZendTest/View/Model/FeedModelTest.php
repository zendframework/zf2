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
use Zend\Feed\Writer\Feed;
use Zend\View\Model\FeedModel;
use Zend\View\Model\FeedModelOptions;
use Zend\View\Variables;
use ZendTest\View\Model\TestAsset\Variable;

class FeedModelTest extends TestCase
{
    public function testDefaultObjectState()
    {
        $model = new FeedModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\FeedModelOptions', $model->getOptions());
        $this->assertEquals(array(), $model->getChildren());
    }

    public function testAllowsPassingVariablesAsViewVariablesInConstructor()
    {
        $variables = new Variables();

        $model  = new FeedModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testAllowsPassingOptionsAsFeedModelOptionsInConstructor()
    {
        $options = new FeedModelOptions();

        $model = new FeedModel(array(), $options);
        $this->assertSame($options, $model->getOptions());
    }

    public function testAllowsPassingVariablesAsArrayInConstructor()
    {
        $variables = array('foo' => new Variable);

        $model = new FeedModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testAllowsPassingOptionsAsArrayInConstructor()
    {
        $options = array('template' => 'foo/bar');

        $model = new FeedModel(array(), $options);
        $this->assertInstanceOf('Zend\View\Model\FeedModelOptions', $model->getOptions());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingBothVariablesAndOptionsArgumentsInConstructor()
    {
        $variables  = array('foo' => 'bar');
        $options    = array('template' => 'foo/bar');

        $model = new FeedModel($variables, $options);
        $this->assertEquals($variables, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\FeedModelOptions', $model->getOptions());
        $this->assertEquals('foo/bar', $model->getOptions()->getTemplate());
    }

    public function testAllowsPassingTraversableArgumentsToVariablesAndOptionsInConstructor()
    {
        $variables = new ArrayObject(array('foo' => 'bar'));
        $options   = new ArrayObject(array('terminal' => false));

        $model = new FeedModel($variables, $options);
        $this->assertSame($variables, $model->getVariables());
        $this->assertInstanceOf('Zend\View\Model\FeedModelOptions', $model->getOptions());
        $this->assertFalse($model->getOptions()->isTerminal());
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects an array');

        $model = new FeedModel();
        $model->setVariables(new stdClass);
    }

    public function testPassingAnInvalidOptionsRaisesAnException()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Model\FeedModelOptions; received "stdClass"');

        $model = new FeedModel();
        $model->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfFeedModelOptions()
    {
        $model = new FeedModel();

        $this->assertInstanceOf(
            'Zend\View\Model\FeedModelOptions',
            $model->getOptions());
    }

    public function testOptionsReturnInstanceOfConsoleModelOptions()
    {
        $model = new FeedModel();

        $this->assertInstanceOf(
            'Zend\View\Model\FeedModelOptions',
            $model->getOptions());
    }

    public function testPassingOverwriteFlagWhenSettingVariablesOverwritesContainer()
    {
        $variables = new Variables(array('foo' => 'bar'));
        $model     = new FeedModel($variables);
        $overwrite = new Variables(array('foo' => 'baz'));
        $model->setVariables($overwrite, true);
        $this->assertSame($overwrite, $model->getVariables());
    }

    public function testFeedGetterCreateFeedInstance()
    {
        $model = new FeedModel();
        $model->setVariables(array('title' => 'foo'));
        $this->assertInstanceOf('Zend\Feed\Writer\Feed', $model->getFeed());
        $this->assertEquals('foo', $model->getFeed()->getTitle());
    }

    public function testFeedIsMutable()
    {
        $feed1 = new Feed;
        $feed1->setTitle('foo');
        $feed2 = new Feed;
        $feed2->setTitle('bar');

        $model = new FeedModel();
        $model->setFeed($feed1);
        $this->assertEquals($feed1->getTitle(), $model->getFeed()->getTitle());
        $model->setFeed($feed2);
        $this->assertEquals($feed2->getTitle(), $model->getFeed()->getTitle());
    }
}
