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
use Zend\View\Model\JsonModelOptions;

class JsonModelOptionsTest extends TestCase
{
    /**
     * @var JsonModelOptions
     */
    protected $options;

    public function setUp()
    {
        $this->options = new JsonModelOptions();

        parent::setUp();
    }

    public function testCaptureToIsNullByDefault()
    {
        $this->assertNull($this->options->captureTo());
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

    public function testTerminateFlagIsTrueByDefault()
    {
        $this->options = new JsonModelOptions();
        $this->assertTrue($this->options->isTerminal());
    }
}
