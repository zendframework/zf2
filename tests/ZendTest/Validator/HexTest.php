<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Hex;

/**
 * @group      Zend_Validator
 */
class HexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Hex
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Hex();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = [
            [1, true],
            [0x1, true],
            [0x123, true],
            ['1', true],
            ['abc123', true],
            ['ABC123', true],
            ['1234567890abcdef', true],
            ['g', false],
            ['1.2', false]
            ];
        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->validator->isValid($element[0]), $element[0]);
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid([1 => 1]));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
