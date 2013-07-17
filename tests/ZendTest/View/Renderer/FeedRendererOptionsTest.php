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
use Zend\View\Renderer\FeedRendererOptions;

class FeedRendererOptionsTest extends TestCase
{
    /**
     * @var FeedRendererOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new FeedRendererOptions();

        parent::setUp();
    }

    public function testFeedTypeIsRssByDefault()
    {
        $this->assertEquals('rss', $this->options->getFeedType());
    }

    public function testFeedTypeIsMutable()
    {
        $this->options->setFeedType('atom');
        $this->assertEquals('atom', $this->options->getFeedType());

        $this->options->setFeedType('rss');
        $this->assertEquals('rss', $this->options->getFeedType());
    }

    public function testFeedTypeBadTypeThrowAnException()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'setFeedType expects a string of either "rss" or "atom"');

        $this->options->setFeedType('foo');
    }

    public function testRendersTreesFlagIsTrueByDefault()
    {
        $this->assertFalse($this->options->canRenderTrees());
    }
}
