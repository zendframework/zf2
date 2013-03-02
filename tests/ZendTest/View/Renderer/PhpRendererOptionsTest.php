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
use Zend\View\Renderer\PhpRendererOptions;

class PhpRendererOptionsTest extends TestCase
{
    /**
     * @var PhpRendererOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new PhpRendererOptions();

        parent::setUp();
    }

    public function testRendersTreesFlagIsFalseByDefault()
    {
        $this->assertFalse($this->options->canRenderTrees());
    }
}
