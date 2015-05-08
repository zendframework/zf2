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
use Zend\Uri\Exception\InvalidArgumentException;

/**
 * @group      Zend_Validator
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Validator\Uri
     */
    protected $validator;

    /**
     * Creates a new Uri Validator object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Validator\Uri();
    }

    public function testHasDefaultSettingsAndLazyLoadsUriHandler()
    {
        $validator = $this->validator;
        $uriHandler = $validator->getUriHandler();
        $this->assertInstanceOf('Zend\Uri\Uri', $uriHandler);
        $this->assertTrue($validator->getAllowRelative());
        $this->assertTrue($validator->getAllowAbsolute());
    }

    public function testConstructorWithArraySetsOptions()
    {
        $uriMock = $this->getMock('Zend\Uri\Uri');
        $validator = new Validator\Uri([
            'uriHandler' => $uriMock,
            'allowRelative' => false,
            'allowAbsolute' => false,
        ]);
        $this->assertEquals($uriMock, $validator->getUriHandler());
        $this->assertFalse($validator->getAllowRelative());
        $this->assertFalse($validator->getAllowAbsolute());
    }

    public function testConstructorWithArgsSetsOptions()
    {
        $uriMock = $this->getMock('Zend\Uri\Uri');
        $validator = new Validator\Uri($uriMock, false, false);
        $this->assertEquals($uriMock, $validator->getUriHandler());
        $this->assertFalse($validator->getAllowRelative());
        $this->assertFalse($validator->getAllowAbsolute());
    }

    public function allowOptionsDataProvider()
    {
        return [
            //    allowAbsolute allowRelative isAbsolute isRelative isValid expects
            [true,         true,         true,      false,     true,   true],
            [true,         true,         false,     true,      true,   true],
            [false,        true,         true,      false,     true,   false],
            [false,        true,         false,     true,      true,   true],
            [true,         false,        true,      false,     true,   true],
            [true,         false,        false,     true,      true,   false],
            [false,        false,        true,      false,     true,   false],
            [false,        false,        false,     true,      true,   false],
            [true,         true,         false,     false,     false,  false],
        ];
    }

    /**
     * @dataProvider allowOptionsDataProvider
     */
    public function testUriHandlerBehaviorWithAllowSettings(
        $allowAbsolute, $allowRelative, $isAbsolute, $isRelative, $isValid, $expects
    ) {
        $uriMock = $this->getMock(
            'Zend\Uri\Uri',
            ['parse', 'isValid', 'isAbsolute', 'isValidRelative']
        );
        $uriMock->expects($this->once())
            ->method('isValid')->will($this->returnValue($isValid));
        $uriMock->expects($this->any())
            ->method('isAbsolute')->will($this->returnValue($isAbsolute));
        $uriMock->expects($this->any())
            ->method('isValidRelative')->will($this->returnValue($isRelative));

        $this->validator->setUriHandler($uriMock)
            ->setAllowAbsolute($allowAbsolute)
            ->setAllowRelative($allowRelative);

        $this->assertEquals($expects, $this->validator->isValid('uri'));
    }

    public function testUriHandlerThrowsExceptionInParseMethodNotValid()
    {
        $uriMock = $this->getMock('Zend\Uri\Uri');
        $uriMock->expects($this->once())
            ->method('parse')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->validator->setUriHandler($uriMock);
        $this->assertFalse($this->validator->isValid('uri'));
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

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertObjectHasAttribute('messageTemplates', $validator);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }

    public function testUriHandlerCanBeSpecifiedAsString()
    {
        $this->validator->setUriHandler('Zend\Uri\Http');
        $this->assertInstanceOf('Zend\Uri\Http', $this->validator->getUriHandler());
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testUriHandlerStringInvalidClassThrowsException()
    {
        $this->validator->setUriHandler('stdClass');
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testUriHandlerInvalidTypeThrowsException()
    {
        $this->validator->setUriHandler(new \stdClass());
    }
}
