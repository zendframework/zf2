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
use Zend\View\Renderer\JsonRendererOptions;

class JsonRendererOptionsTest extends TestCase
{
    /**
     * @var JsonRendererOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new JsonRendererOptions();

        parent::setUp();
    }

    public function testJsonpCallbackIsNullByDefault()
    {
        $this->assertNull($this->options->getJsonpCallback());
    }

    public function testJsonpCallbackIsMutable()
    {
        $this->options->setJsonpCallback(0);
        $this->assertNull($this->options->getJsonpCallback());

        $this->options->setJsonpCallback('');
        $this->assertNull($this->options->getJsonpCallback());

        $this->options->setJsonpCallback('callback');
        $this->assertEquals('callback', $this->options->getJsonpCallback());
    }

    public function testJsonpCallbackIsMutableHasMethod()
    {
        $this->assertFalse($this->options->hasJsonpCallback());

        $this->options->setJsonpCallback(0);
        $this->assertFalse($this->options->hasJsonpCallback());

        $this->options->setJsonpCallback('callback');
        $this->assertTrue($this->options->hasJsonpCallback());
    }

    public function testMergeUnnamedChildrenFlagIsFalseByDefault()
    {
        $this->assertFalse($this->options->canMergeUnnamedChildren());
    }

    public function testMergeUnnamedChildrenFlagIsMutable()
    {
        $this->options->setMergeUnnamedChildren(true);
        $this->assertTrue($this->options->canMergeUnnamedChildren());

        $this->options->setMergeUnnamedChildren(false);
        $this->assertFalse($this->options->canMergeUnnamedChildren());
    }

    public function testRendersTreesFlagIsTrueByDefault()
    {
        $this->assertTrue($this->options->canRenderTrees());
    }
}
