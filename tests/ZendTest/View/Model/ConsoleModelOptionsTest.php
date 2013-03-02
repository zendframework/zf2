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
use Zend\View\Model\ConsoleModelOptions;

class ConsoleModelOptionsTest extends TestCase
{
    /**
     * @var ConsoleModelOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new ConsoleModelOptions();

        parent::setUp();
    }

    public function testCaptureToIsNullByDefault()
    {
        $this->assertNull($this->options->captureTo());
    }

    public function testErrorLevelIsOneByDefault()
    {
        $this->assertEquals(1, $this->options->getErrorLevel());
    }

    public function testErrorLevelIsMutable()
    {
        $this->options->setErrorLevel(8);
        $this->assertEquals(8, $this->options->getErrorLevel());

        $this->options->setErrorLevel(32);
        $this->assertEquals(32, $this->options->getErrorLevel());

        $this->options->setErrorLevel('foo');
        $this->assertEquals(0, $this->options->getErrorLevel());
    }

    public function testTerminateFlagIsTrueByDefault()
    {
        $this->assertTrue($this->options->isTerminal());
    }
}
