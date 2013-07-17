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
use Zend\View\Model\AbstractModelOptions;

class AbstractModelOptionsTest extends TestCase
{
    /**
     * @var AbstractModelOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = $this->getMockForAbstractClass('Zend\View\Model\AbstractModelOptions');

        parent::setUp();
    }

    public function testAppendFlagIsFalseByDefault()
    {
        $this->assertFalse($this->options->isAppend());
    }

    public function testAppendFlagIsMutable()
    {
        $this->options->setAppend(true);

        $this->assertTrue($this->options->isAppend());
    }

    public function testCaptureToIsContentByDefault()
    {
        $this->assertEquals('content', $this->options->captureTo());
    }

    public function testCaptureToIsMutable()
    {
        $this->options->setCaptureTo('foo');

        $this->assertEquals('foo', $this->options->captureTo());
    }

    public function testHasParentFlagIsFalseByDefault()
    {
        $this->assertFalse($this->options->getHasParent());
    }

    public function testHasParentFlagIsMutable()
    {
        $this->options->setHasParent(true);

        $this->assertTrue($this->options->getHasParent());
    }

    public function testTemplateIsEmptyByDefault()
    {
        $this->assertEmpty($this->options->getTemplate());
    }

    public function testTemplateIsMutable()
    {
        $this->options->setTemplate('foo');

        $this->assertEquals('foo', $this->options->getTemplate());
    }

    public function testTerminateFlagIsFalseByDefault()
    {
        $this->assertFalse($this->options->isTerminal());
    }

    public function testTerminateFlagIsMutable()
    {
        $this->options->setTerminal(true);

        $this->assertTrue($this->options->isTerminal());
    }
}
