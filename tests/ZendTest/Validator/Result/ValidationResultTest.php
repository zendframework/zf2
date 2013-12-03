<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\Result;

use Zend\Validator\Result\ValidationResult;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class ValidationResultTest extends \PHPUnit_Framework_TestCase
{
    public function testValidationResultCanBeCreatedWithoutError()
    {
        $validationResult = new ValidationResult('data');
        $this->assertEquals('data', $validationResult->getData());
        $this->assertEmpty($validationResult->getErrorMessages());
        $this->assertEmpty($validationResult->getRawErrorMessages());
        $this->assertEmpty($validationResult->getMessageVariables());
        $this->assertTrue($validationResult->isValid());
    }

    public function testStringErrorIsConvertedToArray()
    {
        $validationResult = new ValidationResult('data', 'An error message');
        $this->assertInternalType('array', $validationResult->getRawErrorMessages());
        $this->assertCount(1, $validationResult->getRawErrorMessages());
        $this->assertCount(1, $validationResult->getErrorMessages());
    }

    public function testCanMerge()
    {
        $validationResult1 = new ValidationResult('data', 'First error', ['key1' => 'var1']);
        $validationResult2 = new ValidationResult('data', 'Second error', ['key2' => 'var2']);

        $validationResult1->merge($validationResult2);

        $this->assertEquals('data', $validationResult1->getData());
        $this->assertCount(2, $validationResult1->getRawErrorMessages());
        $this->assertCount(2, $validationResult1->getMessageVariables());
        $this->assertFalse($validationResult1->isValid());
    }

    public function testCanGetErrorMessagesWithoutInterpolation()
    {
        $validationResult = new ValidationResult('data', 'An error message');
        $expected         = array('An error message');

        $this->assertEquals($expected, $validationResult->getErrorMessages());
        $this->assertEquals($expected, $validationResult->getRawErrorMessages());
    }

    public function testCanInterpolate()
    {
        $validationResult    = new ValidationResult('data', 'Length must be %min%', ['%min%' => 4]);
        $expectedRaw         = array('Length must be %min%');
        $expectedInterpolate = array('Length must be 4');

        $this->assertEquals(['%min%' => 4], $validationResult->getMessageVariables());
        $this->assertEquals($expectedInterpolate, $validationResult->getErrorMessages());
        $this->assertEquals($expectedRaw, $validationResult->getRawErrorMessages());
    }

    public function testCanInterpolateComplex()
    {
        $validationResult = new ValidationResult(
            'data',
            array('Length must be %min%', 'Does not validate %pattern%'),
            ['%min%' => 4, '%pattern%' => 'abc']
        );

        $expectedRaw         = array('Length must be %min%', 'Does not validate %pattern%');
        $expectedInterpolate = array('Length must be 4', 'Does not validate abc');

        $this->assertEquals(['%min%' => 4, '%pattern%' => 'abc'], $validationResult->getMessageVariables());
        $this->assertEquals($expectedInterpolate, $validationResult->getErrorMessages());
        $this->assertEquals($expectedRaw, $validationResult->getRawErrorMessages());
    }

    public function testCanSerialize()
    {
        $validationResult = new ValidationResult('data', 'Length must be %min%', ['%min%' => 4]);

        $serialize   = serialize($validationResult);
        $unserialize = unserialize($serialize);

        $this->assertFalse($unserialize->isValid());
        $this->assertEquals('data', $unserialize->getData());
        $this->assertEquals(['%min%' => 4], $unserialize->getMessageVariables());
        $this->assertEquals(['Length must be %min%'], $unserialize->getRawErrorMessages());
        $this->assertEquals(['Length must be 4'], $unserialize->getErrorMessages());
    }

    public function testCanJsonSerialize()
    {
        $validationResult = new ValidationResult('data', 'Length must be %min%', ['%min%' => 4]);
        $json             = json_encode($validationResult);

        $this->assertEquals('["Length must be 4"]', $json);
    }
}
