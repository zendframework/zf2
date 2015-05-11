<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Config;
use Zend\Validator\CreditCard;

/**
 * @group      Zend_Validator
 */
class CreditCardTest extends \PHPUnit_Framework_TestCase
{
    public static function basicValues()
    {
        return [
            ['4111111111111111', true],
            ['5404000000000001', true],
            ['374200000000004', true],
            ['4444555566667777', false],
            ['ABCDEF', false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicValues
     */
    public function testBasic($input, $expected)
    {
        $validator      = new CreditCard();
        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new CreditCard();
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that get and setType works as expected
     *
     * @return void
     */
    public function testGetSetType()
    {
        $validator = new CreditCard();
        $this->assertEquals(11, count($validator->getType()));

        $validator->setType(CreditCard::MAESTRO);
        $this->assertEquals([CreditCard::MAESTRO], $validator->getType());

        $validator->setType(
            [
                CreditCard::AMERICAN_EXPRESS,
                CreditCard::MAESTRO
            ]
        );
        $this->assertEquals(
            [
                CreditCard::AMERICAN_EXPRESS,
                CreditCard::MAESTRO
            ],
            $validator->getType()
        );

        $validator->addType(
            CreditCard::MASTERCARD
        );
        $this->assertEquals(
            [
                CreditCard::AMERICAN_EXPRESS,
                CreditCard::MAESTRO,
                CreditCard::MASTERCARD
            ],
            $validator->getType()
        );
    }

    public static function visaValues()
    {
        return [
            ['4111111111111111', true],
            ['5404000000000001', false],
            ['374200000000004', false],
            ['4444555566667777', false],
            ['ABCDEF', false],
        ];
    }

    /**
     * Test specific provider
     *
     * @dataProvider visaValues
     */
    public function testProvider($input, $expected)
    {
        $validator      = new CreditCard(CreditCard::VISA);
        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Test non string input
     *
     * @return void
     */
    public function testIsValidWithNonString()
    {
        $validator = new CreditCard(CreditCard::VISA);
        $this->assertFalse($validator->isValid(['something']));
    }

    public static function serviceValues()
    {
        return [
            ['4111111111111111', false],
            ['5404000000000001', false],
            ['374200000000004', false],
            ['4444555566667777', false],
            ['ABCDEF', false],
        ];
    }

    /**
     * Test service class with invalid validation
     *
     * @dataProvider serviceValues
     */
    public function testServiceClass($input, $expected)
    {
        $validator = new CreditCard();
        $this->assertEquals(null, $validator->getService());
        $validator->setService(['ZendTest\Validator\CreditCardTest', 'staticCallback']);
        $this->assertEquals($expected, $validator->isValid($input));
    }

    public static function optionsValues()
    {
        return [
            ['4111111111111111', false],
            ['5404000000000001', false],
            ['374200000000004', false],
            ['4444555566667777', false],
            ['ABCDEF', false],
        ];
    }

    /**
     * Test non string input
     *
     * @dataProvider optionsValues
     */
    public function testConstructionWithOptions($input, $expected)
    {
        $validator = new CreditCard(
            [
                'type' => CreditCard::VISA,
                'service' => ['ZendTest\Validator\CreditCardTest', 'staticCallback']
            ]
        );

        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Data provider
     *
     * @return string[][]|bool[][]
     */
    public function jcbValues()
    {
        return [
            ['3566003566003566', true],
            ['3528000000000007', true],
            ['3528000000000007', true],
            ['3528000000000007', true],
            ['3088185545477406', false],
            ['3158854390756173', false],
            ['3088936920428541', false],
            ['213193692042852', true],
            ['180012362524156', true],
        ];
    }

    /**
     * Test JCB number validity
     *
     * @dataProvider jcbValues
     *
     * @param string $input
     * @param bool   $expected
     *
     * @group 6278
     * @group 6927
     */
    public function testJcbCard($input, $expected)
    {
        $validator = new CreditCard(['type' => CreditCard::JCB]);

        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Test an invalid service class
     *
     * @return void
     */
    public function testInvalidServiceClass()
    {
        $validator = new CreditCard();
        $this->assertEquals(null, $validator->getService());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Invalid callback given');
        $validator->setService(['ZendTest\Validator\CreditCardTest', 'nocallback']);
    }

    /**
     * Test a config object
     *
     * @return void
     */
    public function testConfigObject()
    {
        $options = ['type' => 'Visa'];
        $config = new Config\Config($options, false);

        $validator = new CreditCard($config);
        $this->assertEquals(['Visa'], $validator->getType());
    }

    /**
     * Test optional parameters with config object
     *
     * @return void
     */
    public function testOptionalConstructorParameterByConfigObject()
    {
        $config = new Config\Config(['type' => 'Visa', 'service' => ['ZendTest\Validator\CreditCardTest', 'staticCallback']]);

        $validator = new CreditCard($config);
        $this->assertEquals(['Visa'], $validator->getType());
        $this->assertEquals(['ZendTest\Validator\CreditCardTest', 'staticCallback'], $validator->getService());
    }

    /**
     * Test optional constructor parameters
     *
     * @return void
     */
    public function testOptionalConstructorParameter()
    {
        $validator = new CreditCard('Visa', ['ZendTest\Validator\CreditCardTest', 'staticCallback']);
        $this->assertEquals(['Visa'], $validator->getType());
        $this->assertEquals(['ZendTest\Validator\CreditCardTest', 'staticCallback'], $validator->getService());
    }

    /**
     * @group ZF-9477
     */
    public function testMultiInstitute()
    {
        $validator      = new CreditCard(['type' => CreditCard::MASTERCARD]);
        $this->assertFalse($validator->isValid('4111111111111111'));
        $message = $validator->getMessages();
        $this->assertContains('not from an allowed institute', current($message));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new CreditCard();
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public static function staticCallback($value)
    {
        return false;
    }
}
