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

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\View\Renderer\ConsoleRenderer;
use Zend\View\Model\ConsoleModel;
use Zend\View\Model\FeedModel;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ConsoleRendererTest extends TestCase
{
    /**
     * @var ConsoleRenderer
     */
    protected $renderer;

    public function setUp()
    {
        $this->renderer = new ConsoleRenderer();
    }

    public function testCantRenderFeedModels()
    {
        $model = new FeedModel(array('foo' => 'bar'));
        $test  = $this->renderer->render($model);

        $this->assertEmpty($test);
    }

    public function testCantRenderJsonModels()
    {
        $model = new JsonModel(array('foo' => 'bar'));
        $test  = $this->renderer->render($model);

        $this->assertEmpty($test);
    }

    public function testCantRenderViewModels()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $test  = $this->renderer->render($model);

        $this->assertEmpty($test);
    }

    public function testRenderConsoleObject()
    {
        $model = new ConsoleModel();
        $model->setResult('foo');

        $rendered  = $this->renderer->render($model);
        $this->assertEquals('foo', $rendered);
    }

    public function testRenderConsoleObjectWithChildrens()
    {
        $model1 = new ConsoleModel();
        $model1->setResult('foo');

        $model2 = new ConsoleModel();
        $model2->setResult('bar');

        $model1->addChild($model2);

        $rendered  = $this->renderer->render($model1);
        $this->assertEquals('foobar', $rendered);
    }

    public function testFilterChainReturnInstanceOfFilterChain()
    {
        $this->assertInstanceOf(
            'Zend\Filter\FilterChain',
            $this->renderer->getFilterChain());
    }

    public function testEngineIsInstanceOfConsoleRenderer()
    {
        $this->assertInstanceOf('Zend\View\Renderer\ConsoleRenderer', $this->renderer->getEngine());
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Renderer\ConsoleRendererOptions; received "stdClass"');

        $this->renderer->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfConsoleRendererOptions()
    {
        $this->assertInstanceOf(
            'Zend\View\Renderer\ConsoleRendererOptions',
            $this->renderer->getOptions());
    }
}
