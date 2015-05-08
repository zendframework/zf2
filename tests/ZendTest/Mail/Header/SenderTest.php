<?php
/**
 * Zend Framework (http://framework.zend/)
 *
 * @link      http://github/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend)
 * @license   http://framework.zend/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Address;
use Zend\Mail\Header;

/**
 * @group      Zend_Mail
 */
class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function testFromStringCreatesValidReceivedHeader()
    {
        $sender = Header\Sender::fromString('Sender: <foo@bar>');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderInterface', $sender);
        $this->assertInstanceOf('Zend\Mail\Header\Sender', $sender);
    }

    public function testGetFieldNameReturnsHeaderName()
    {
        $sender = new Header\Sender();
        $this->assertEquals('Sender', $sender->getFieldName());
    }

    /**
     * @dataProvider validSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     * @param string $expectedFieldValue,
     * @param string $encodedValue
     * @param string $encoding
     */
    public function testParseValidSenderHeader($email, $name, $expectedFieldValue, $encodedValue, $encoding)
    {
        $header = Header\Sender::fromString('Sender:' . $encodedValue);

        $this->assertEquals($expectedFieldValue, $header->getFieldValue());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    /**
     * @dataProvider invalidSenderEncodedDataProvider
     * @group ZF2015-04
     * @param string $decodedValue
     * @param string $expectedException
     * @param string|null $expectedExceptionMessage
     */
    public function testParseInvalidSenderHeaderThrowException(
        $decodedValue,
        $expectedException,
        $expectedExceptionMessage
    ) {
        $this->setExpectedException($expectedException, $expectedExceptionMessage);
        Header\Sender::fromString('Sender:' . $decodedValue);
    }

    /**
     * @dataProvider validSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     * @param string $encodedValue
     * @param string $expectedFieldValue,
     * @param string $encoding
     */
    public function testSetAddressValidValue($email, $name, $expectedFieldValue, $encodedValue, $encoding)
    {
        $header = new Header\Sender();
        $header->setAddress($email, $name);

        $this->assertEquals($expectedFieldValue, $header->getFieldValue());
        $this->assertEquals('Sender: ' . $encodedValue, $header->toString());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    /**
     * @dataProvider invalidSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     */
    public function testSetAddressInvalidValue($email, $name)
    {
        $header = new Header\Sender();
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException');
        $header->setAddress($email, $name);
    }

    /**
     * @dataProvider validSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     * @param string $expectedFieldValue,
     * @param string $encodedValue
     * @param string $encoding
     */
    public function testSetAddressValidAddressObject($email, $name, $expectedFieldValue, $encodedValue, $encoding)
    {
        $address = new Address($email, $name);

        $header = new Header\Sender();
        $header->setAddress($address);

        $this->assertSame($address, $header->getAddress());
        $this->assertEquals($expectedFieldValue, $header->getFieldValue());
        $this->assertEquals('Sender: ' . $encodedValue, $header->toString());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    public function validSenderDataProvider()
    {
        return [
            // Description => [sender address, sender name, getFieldValue, encoded version, encoding],
            'ASCII address' => [
                'foo@bar',
                null,
                '<foo@bar>',
                '<foo@bar>',
                'ASCII'
            ],
            'ASCII name' => [
                'foo@bar',
                'foo',
                'foo <foo@bar>',
                'foo <foo@bar>',
                'ASCII'
            ],
            'UTF-8 name' => [
                'foo@bar',
                'ázÁZ09',
                'ázÁZ09 <foo@bar>',
                '=?UTF-8?Q?=C3=A1z=C3=81Z09?= <foo@bar>',
                'UTF-8'
            ],
        ];
    }

    public function invalidSenderDataProvider()
    {
        $mailInvalidArgumentException = 'Zend\Mail\Exception\InvalidArgumentException';

        return [
            // Description => [sender address, sender name, exception class, exception message],
            'Empty' => ['', null, $mailInvalidArgumentException, null],
            'any ASCII' => ['azAZ09-_', null, $mailInvalidArgumentException, null],
            'any UTF-8' => ['ázÁZ09-_', null, $mailInvalidArgumentException, null],

            // CRLF @group ZF2015-04 cases
            ["foo@bar\n", null, $mailInvalidArgumentException, null],
            ["foo@bar\r", null, $mailInvalidArgumentException, null],
            ["foo@bar\r\n", null, $mailInvalidArgumentException, null],
            ["foo@bar", "\r", $mailInvalidArgumentException, null],
            ["foo@bar", "\n", $mailInvalidArgumentException, null],
            ["foo@bar", "\r\n", $mailInvalidArgumentException, null],
            ["foo@bar", "foo\r\nevilBody", $mailInvalidArgumentException, null],
            ["foo@bar", "\r\nevilBody", $mailInvalidArgumentException, null],
        ];
    }

    public function invalidSenderEncodedDataProvider()
    {
        $mailInvalidArgumentException = 'Zend\Mail\Exception\InvalidArgumentException';
        $headerInvalidArgumentException = 'Zend\Mail\Header\Exception\InvalidArgumentException';

        return [
            // Description => [decoded format, exception class, exception message],
            'Empty' => ['', $mailInvalidArgumentException, null],
            'any ASCII' => ['azAZ09-_', $mailInvalidArgumentException, null],
            'any UTF-8' => ['ázÁZ09-_', $mailInvalidArgumentException, null],
            ["xxx yyy\n", $mailInvalidArgumentException, null],
            ["xxx yyy\r\n", $mailInvalidArgumentException, null],
            ["xxx yyy\r\n\r\n", $mailInvalidArgumentException, null],
            ["xxx\r\ny\r\nyy", $mailInvalidArgumentException, null],
            ["foo\r\n@\r\nbar", $mailInvalidArgumentException, null],

            ["ázÁZ09 <foo@bar>", $headerInvalidArgumentException, null],
            'newline' => ["<foo@bar>\n", $headerInvalidArgumentException, null],
            'cr-lf' => ["<foo@bar>\r\n", $headerInvalidArgumentException, null],
            'cr-lf-wsp' => ["<foo@bar>\r\n\r\n", $headerInvalidArgumentException, null],
            'multiline' => ["<foo\r\n@\r\nbar>", $headerInvalidArgumentException, null],
        ];
    }
}
