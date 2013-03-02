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
use Zend\View\Model\FeedModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\FeedRenderer;

class FeedRendererTest extends TestCase
{
    /**
     * @var FeedRenderer
     */
    protected $renderer;

    public function setUp()
    {
        $this->renderer = new FeedRenderer();

        date_default_timezone_set('Europe/Prague');
    }

    protected function getFeedData($type)
    {
        return array(
            'copyright' => date('Y'),
            'date_created' => time(),
            'date_modified' => time(),
            'last_build_date' => time(),
            'description' => __CLASS__,
            'id' => 'http://framework.zend.com/',
            'language' => 'en_US',
            'feed_link' => array(
                'link' => 'http://framework.zend.com/feed.xml',
                'type' => $type,
            ),
            'link' => 'http://framework.zend.com/feed.xml',
            'title' => 'Testing',
            'encoding' => 'UTF-8',
            'base_url' => 'http://framework.zend.com/',
            'entries' => array(
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/1',
                    'link' => 'http://framework.zend.com/1',
                    'title' => 'Test 1',
                ),
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/2',
                    'link' => 'http://framework.zend.com/2',
                    'title' => 'Test 2',
                ),
            ),
        );
    }

    public function testRendersFeedModelAccordingToTypeProvidedInModel()
    {
        $model = new FeedModel($this->getFeedData('atom'));
        $model->getOptions()->setFeedType('atom');

        $xml = $this->renderer->render($model);

        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testRendersFeedModelAccordingToRenderTypeIfNoTypeProvidedInModel()
    {
        $this->renderer->getOptions()->setFeedType('atom');
        $model = new FeedModel($this->getFeedData('atom'));

        $xml = $this->renderer->render($model);

        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testCastsViewModelToFeedModelUsingRendererFeedTypeIfNoFeedTypeOptionInModel()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Zend\View\Renderer\FeedRenderer::render expects '
            . 'a "Zend\View\Model\FeedModel" or a string feed type as the first '
            . 'argument; received "Zend\View\Model\ViewModel"');

        $model = new ViewModel($this->getFeedData('atom'));

        $this->renderer->getOptions()->setFeedType('atom');
        $this->renderer->render($model);
    }

    public function testStringModelWithValuesProvidedCastsToFeed()
    {
        $xml = $this->renderer->render('atom', $this->getFeedData('atom'));

        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testNonStringNonModelArgumentRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects');

        $this->renderer->render(array('foo'));
    }

    public function testSettingUnacceptableFeedTypeRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects a string of either "rss" or "atom"');

        $this->renderer->getOptions()->setFeedType('foobar');
    }

    public function testEngineIsInstanceOfFeedRenderer()
    {
        $this->assertInstanceOf('Zend\View\Renderer\FeedRenderer', $this->renderer->getEngine());
    }

    public function testOptionsRaisesAnExceptionPassingInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'Expected instance of Zend\View\Renderer\FeedRendererOptions; received "stdClass"');

        $this->renderer->setOptions(new stdClass);
    }

    public function testOptionsReturnInstanceOfFeedRendererOptions()
    {
        $this->assertInstanceOf(
            'Zend\View\Renderer\FeedRendererOptions',
            $this->renderer->getOptions());
    }
}
