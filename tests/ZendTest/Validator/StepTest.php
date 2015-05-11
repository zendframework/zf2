<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator;

/**
 * @group      Zend_Validator
 */
class StepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Step object
     *
     * @var \Zend\Validator\Step
     */
    protected $_validator;

    /**
     * Creates a new Zend\Validator\Step object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\Step();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        // By default, baseValue == 0 and step == 1
        $valuesExpected = [
            [1.00, true],
            [0.00, true],
            [2, true],
            [3, true],
            [2.1, false],
            ['2', true],
            ['1', true],
            ['1.2', false],
            [1.01, false],
            ['not a scalar', false]
        ];

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    public function testDecimalBaseValue()
    {
        $valuesExpected = [
            [1.1, false],
            [0.1, true],
            [2.1, true],
            [3.1, false],
            ['2.1', true],
            ['1.1', false],
            [1.11, false],
            ['not a scalar', false]
        ];

        $validator = new Validator\Step([
            'baseValue' => 0.1,
            'step'      => 2
        ]);

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    public function testDecimalStep()
    {
        $valuesExpected = [
            [1.1, false],
            [0.1, false],
            [2.1, true],
            [3.1, false],
            [4.2, true],
            [6.3, true],
            [8.4, true],
            [10.5, true],
            [12.6, true],
            [14.7, true],
            [16.8, true],
            [18.9, true],
            [21.0, true],
            ['2.1', true],
            ['1.1', false],
            [1.11, false],
            ['not a scalar', false]
        ];

        $validator = new Validator\Step([
            'baseValue' => 0,
            'step'      => 2.1
        ]);

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    public function testDecimalStep2()
    {
        $valuesExpected = [
            [0.01, true],
            [0.02, true],
            [0.03, true],
            [0.04, true],
            [0.05, true],
            [0.06, true],
            [0.07, true],
            [0.08, true],
            [0.09, true],
            [0.001, false],
            [0.002, false],
            [0.003, false],
            [0.004, false],
            [0.005, false],
            [0.006, false],
            [0.007, false],
            [0.008, false],
            [0.009, false]
        ];

        $validator = new Validator\Step([
            'baseValue' => 0,
            'step'      => 0.01
        ]);

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->_validator->getMessages());
    }

    /**
     * Ensures that set/getBaseValue() works
     */
    public function testCanSetBaseValue()
    {
        $this->_validator->setBaseValue(2);
        $this->assertEquals('2', $this->_validator->getBaseValue());
    }

    /**
     * Ensures that set/getStep() works
     */
    public function testCanSetStepValue()
    {
        $this->_validator->setStep(2);
        $this->assertEquals('2', $this->_validator->getStep());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Step();
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testSetStepFloat()
    {
        $step = 0.01;
        $this->_validator->setStep($step);
        $this->assertAttributeSame($step, 'step', $this->_validator);
    }

    public function testSetStepString()
    {
        $step = '0.01';
        $this->_validator->setStep($step);
        $this->assertAttributeSame((float) $step, 'step', $this->_validator);
    }
}
