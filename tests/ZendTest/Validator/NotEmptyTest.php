<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use stdClass;
use Zend\Validator\NotEmpty;

/**
 * @group      Zend_Validator
 */
class NotEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotEmpty
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new NotEmpty();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * ZF-6708 introduces a change for validating integer 0; it is a valid
     * integer value. '0' is also valid.
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @group ZF-6708
     * @return void
     * @dataProvider basicProvider
     */
    public function testBasic($value, $valid)
    {
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and expected validity for the basic test
     *
     * @return array
     */
    public function basicProvider()
    {
        return [
            ['word', true],
            ['', false],
            ['    ', false],
            ['  word  ', true],
            ['0', true],
            [1, true],
            [0, true],
            [true, true],
            [false, false],
            [null, false],
            [[], false],
            [[5], true],
            [0.0, false],
            [1.0, true],
            [new stdClass(), true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider booleanProvider
     */
    public function testOnlyBoolean($value, $valid)
    {
        $this->validator->setType(NotEmpty::BOOLEAN);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function booleanProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider integerOnlyProvider
     */
    public function testOnlyInteger($value, $valid)
    {
        $this->validator->setType(NotEmpty::INTEGER);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for when the validator is testing empty integer values
     *
     * @return array
     */
    public function integerOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, false],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider floatOnlyProvider
     */
    public function testOnlyFloat($value, $valid)
    {
        $this->validator->setType(NotEmpty::FLOAT);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function floatOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, true],
            [1, true],
            [0.0, false],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider stringOnlyProvider
     */
    public function testOnlyString($value, $valid)
    {
        $this->validator->setType(NotEmpty::STRING);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function stringOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }
    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider zeroOnlyProvider
     */
    public function testOnlyZero($value, $valid)
    {
        $this->validator->setType(NotEmpty::ZERO);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function zeroOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider arrayOnlyProvider
     */
    public function testOnlyArray($value, $valid)
    {
        $this->validator->setType(NotEmpty::EMPTY_ARRAY);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function arrayOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], false],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider nullOnlyProvider
     */
    public function testOnlyNull($value, $valid)
    {
        $this->validator->setType(NotEmpty::NULL);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function nullOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider phpOnlyProvider
     */
    public function testOnlyPHP($value, $valid)
    {
        $this->validator->setType(NotEmpty::PHP);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function phpOnlyProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, false],
            [1, true],
            [0.0, false],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], false],
            [['xxx'], true],
            [null, false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider spaceOnlyProvider
     */
    public function testOnlySpace($value, $valid)
    {
        $this->validator->setType(NotEmpty::SPACE);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function spaceOnlyProvider()
    {
        return [
            [false, true],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider onlyAllProvider
     */
    public function testOnlyAll($value, $valid)
    {
        $this->validator->setType(NotEmpty::ALL);
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function onlyAllProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, false],
            [1, true],
            [0.0, false],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], false],
            [['xxx'], true],
            [null, false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider arrayConstantNotationProvider
     */
    public function testArrayConstantNotation($value, $valid)
    {
        $this->validator = new NotEmpty(
            [
                'type' => [
                    NotEmpty::ZERO,
                    NotEmpty::STRING,
                    NotEmpty::BOOLEAN
                ]
            ]
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function arrayConstantNotationProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider arrayConfigNotationProvider
     */
    public function testArrayConfigNotation($value, $valid)
    {
        $this->validator = new NotEmpty(
            [
                'type' => [
                    NotEmpty::ZERO,
                    NotEmpty::STRING,
                    NotEmpty::BOOLEAN],
                'test' => false
            ]
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function arrayConfigNotationProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider multiConstantNotationProvider
     */
    public function testMultiConstantNotation($value, $valid)
    {
        $this->validator = new NotEmpty(
            NotEmpty::ZERO + NotEmpty::STRING + NotEmpty::BOOLEAN
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider multiConstantNotationProvider
     */
    public function testMultiConstantBooleanOrNotation($value, $valid)
    {
        $this->validator = new NotEmpty(
            NotEmpty::ZERO | NotEmpty::STRING | NotEmpty::BOOLEAN
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function multiConstantNotationProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }
    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider stringNotationProvider
     */
    public function testStringNotation($value, $valid)
    {
        $this->validator = new NotEmpty(
            [
                'type' => ['zero', 'string', 'boolean']
            ]
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function stringNotationProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }


    /**
     * Ensures that the validator follows expected behavior so if a string is specified more than once, it doesn't
     * cause different validations to run
     *
     * @param string  $string   Array of string type values
     * @param integer $expected Expected type setting value
     *
     * @return void
     *
     * @dataProvider duplicateStringSettingProvider
     */
    public function testStringNotationWithDuplicate($string, $expected)
    {
        $type = [$string, $string];
        $this->validator->setType($type);

        $this->assertEquals($expected, $this->validator->getType());
    }

    /**
     * Data provider for testStringNotationWithDuplicate method. Provides a string which will be duplicated. The test
     * ensures that setting a string value more than once only turns on the appropriate bit once
     *
     * @return array
     */
    public function duplicateStringSettingProvider()
    {
        return [
            ['boolean',      NotEmpty::BOOLEAN],
            ['integer',      NotEmpty::INTEGER],
            ['float',        NotEmpty::FLOAT],
            ['string',       NotEmpty::STRING],
            ['zero',         NotEmpty::ZERO],
            ['array',        NotEmpty::EMPTY_ARRAY],
            ['null',         NotEmpty::NULL],
            ['php',          NotEmpty::PHP],
            ['space',        NotEmpty::SPACE],
            ['object',       NotEmpty::OBJECT],
            ['objectstring', NotEmpty::OBJECT_STRING],
            ['objectcount',  NotEmpty::OBJECT_COUNT],
            ['all',          NotEmpty::ALL],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider singleStringNotationProvider
     */
    public function testSingleStringNotation($value, $valid)
    {
        $this->validator = new NotEmpty(
            'boolean'
        );
        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function singleStringNotationProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', true],
            ['abc', true],
            ['0', true],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @return void
     *
     * @dataProvider configObjectProvider
     */
    public function testConfigObject($value, $valid)
    {
        $options = ['type' => 'all'];
        $config  = new \Zend\Config\Config($options);

        $this->validator = new NotEmpty(
            $config
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function configObjectProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, false],
            [1, true],
            [0.0, false],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], false],
            [['xxx'], true],
            [null, false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseType()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Unknown');
        $this->validator->setType(true);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertEquals($this->validator->getDefaultType(), $this->validator->getType());
    }

    /**
     * @group ZF-3236
     */
    public function testStringWithZeroShouldNotBeTreatedAsEmpty()
    {
        $this->assertTrue($this->validator->isValid('0'));
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
        $v2 = new NotEmpty();
        $this->assertTrue($this->validator->isValid($v2));
    }

    /**
     * @ZF-8767
     *
     * @return void
     */
    public function testZF8767()
    {
        $valid = new NotEmpty(NotEmpty::STRING);

        $this->assertFalse($valid->isValid(''));
        $messages = $valid->getMessages();
        $this->assertArrayHasKey('isEmpty', $messages);
        $this->assertContains("can't be empty", $messages['isEmpty']);
    }

    /**
     * @return void
     */
    public function testObjects()
    {
        $valid = new NotEmpty(NotEmpty::STRING);
        $object = new stdClass();

        $this->assertFalse($valid->isValid($object));

        $valid = new NotEmpty(NotEmpty::OBJECT);
        $this->assertTrue($valid->isValid($object));
    }

    /**
     * @return void
     */
    public function testStringObjects()
    {
        $valid = new NotEmpty(NotEmpty::STRING);

        $object = $this->getMockBuilder('\stdClass')
            ->setMethods(['__toString'])
            ->getMock();

        $object->expects($this->atLeastOnce())
            ->method('__toString')
            ->will($this->returnValue('Test'));

        $this->assertFalse($valid->isValid($object));

        $valid = new NotEmpty(NotEmpty::OBJECT_STRING);
        $this->assertTrue($valid->isValid($object));

        $object = $this->getMockBuilder('\stdClass')
            ->setMethods(['__toString'])
            ->getMock();
        $object->expects($this->atLeastOnce())
            ->method('__toString')
            ->will($this->returnValue(''));

        $this->assertFalse($valid->isValid($object));
    }

    /**
     * @group ZF-11566
     *
     * @param mixed $value Value to test
     * @param boolean $valid Expected validity of value
     *
     * @dataProvider arrayConfigNotationWithoutKeyProvider
     */
    public function testArrayConfigNotationWithoutKey($value, $valid)
    {
        $this->validator = new NotEmpty(
            ['zero', 'string', 'boolean']
        );

        $this->checkValidationValue($value, $valid);
    }

    /**
     * Provides values and their expected validity for boolean empty
     *
     * @return array
     */
    public function arrayConfigNotationWithoutKeyProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, true],
            [1, true],
            [0.0, true],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], true],
            [['xxx'], true],
            [null, true],
        ];
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals(
            $validator->getOption('messageTemplates'),
            'messageTemplates',
            $validator
        );
    }

    public function testTypeAutoDetectionHasNoSideEffect()
    {
        $validator = new NotEmpty(['translatorEnabled' => true]);
        $this->assertEquals($validator->getDefaultType(), $validator->getType());
    }

    public function testDefaultType()
    {
        $this->assertSame(
            NotEmpty::BOOLEAN
                | NotEmpty::FLOAT
                | NotEmpty::STRING
                | NotEmpty::EMPTY_ARRAY
                | NotEmpty::NULL
                | NotEmpty::SPACE
                | NotEmpty::OBJECT,
            $this->validator->getDefaultType()
        );
    }

    /**
     * Checks that the validation value matches the expected validity
     *
     * @param mixed $value Value to validate
     * @param bool  $valid Expected validity
     *
     * @return void
     */
    protected function checkValidationValue($value, $valid)
    {
        $isValid = $this->validator->isValid($value);

        if ($valid) {
            $this->assertTrue($isValid);
        } else {
            $this->assertFalse($isValid);
        }
    }
}
