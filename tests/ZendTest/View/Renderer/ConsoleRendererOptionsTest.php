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
use Zend\View\Renderer\ConsoleRendererOptions;

class ConsoleRendererOptionsTest extends TestCase
{
    /**
     * @var ConsoleRendererOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new ConsoleRendererOptions();

        parent::setUp();
    }

    public function testRendersTreesFlagIsTrueByDefault()
    {
        $this->assertTrue($this->options->canRenderTrees());
    }
}
