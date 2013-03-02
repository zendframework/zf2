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
use Zend\View\Renderer\AbstractRendererOptions;

class AbstractRendererOptionsTest extends TestCase
{
    /**
     * @var AbstractRendererOptions
     */
    protected $renderer;

    public function setUp()
    {
        $this->renderer = $this->getMockForAbstractClass('Zend\View\Renderer\AbstractRendererOptions');

        parent::setUp();
    }

    public function testCanRenderTreesFlagIsTrueByDefault()
    {
        $this->assertTrue($this->renderer->canRenderTrees());
    }

    public function testCanRenderTreesFlagIsMutable()
    {
        $this->renderer->setCanRenderTrees(false);
        $this->assertFalse($this->renderer->canRenderTrees());

        $this->renderer->setCanRenderTrees(true);
        $this->assertTrue($this->renderer->canRenderTrees());
    }
}
