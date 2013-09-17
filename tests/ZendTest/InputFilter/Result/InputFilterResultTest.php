<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter\Result;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\Result\InputFilterResult;

class InputFilterResutTest extends TestCase
{
    public function testIsValidIfNoErrorMessages()
    {
        $inputFilterResult = new InputFilterResult(array(), array());
        $this->assertTrue($inputFilterResult->isValid());

        $inputFilterResult = new InputFilterResult(array(), array(), array());
        $this->assertTrue($inputFilterResult->isValid());
    }

    public function testIsInvalidIfErrorMessages()
    {
        $inputFilterResult = new InputFilterResult(array(), array(), array('this' => 'is not valid'));
        $this->assertFalse($inputFilterResult->isValid());
    }

    public function testCanSerializeErrorMessages()
    {
        $inputFilterResult = new InputFilterResult(
            array(),
            array(),
            array('firstName' => 'Should not be empty')
        );

        $serialized   = serialize($inputFilterResult);
        $unserialized = unserialize($serialized);

        $this->assertEquals(array('firstName' => 'Should not be empty'), $unserialized->getErrorMessages());
    }

    public function testCanSerializeToJson()
    {
        $inputFilterResult = new InputFilterResult(
            array(),
            array(),
            array('firstName' => 'Should not be empty')
        );

        $encoded = json_encode($inputFilterResult);
        $this->assertEquals('{"firstName":"Should not be empty"}', $encoded);
    }
}
