<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Ldap\Converter;

use DateTime;
use DateTimeZone;
use stdClass;
use Zend\Ldap\Converter\Converter;

/**
 * @group      Zend_Ldap
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testAsc2hex32()
    {
        $expected = '\00\01\02\03\04\05\06\07\08\09\0a\0b\0c\0d\0e\0f\10\11\12\13\14\15\16\17\18\19' .
                    '\1a\1b\1c\1d\1e\1f !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`' .
                    'abcdefghijklmnopqrstuvwxyz{|}~';
        $str      = '';
        for ($i = 0; $i < 127; $i++) {
            $str .= chr($i);
        }
        $this->assertEquals($expected, Converter::ascToHex32($str));
    }

    public function testHex2asc()
    {
        $expected = '';
        for ($i = 0; $i < 127; $i++) {
            $expected .= chr($i);
        }

        $str = '\00\01\02\03\04\05\06\07\08\09\0a\0b\0c\0d\0e\0f\10\11\12\13\14\15\16\17\18\19\1a\1b' .
               '\1c\1d\1e\1f !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefg' .
               'hijklmnopqrstuvwxyz{|}~';
        $this->assertEquals($expected, Converter::hex32ToAsc($str));
    }

    /**
     * @dataProvider toLdapDateTimeProvider
     */
    public function testToLdapDateTime($convert, $expect)
    {
        $result = Converter::toLdapDatetime($convert['date'], $convert['utc']);
        $this->assertEquals($expect, $result);
    }

    public function toLdapDateTimeProvider()
    {
        $tz = new DateTimeZone('UTC');
        return [
            [['date'=> 0,
                        'utc' => true], '19700101000000Z'],
            [['date'=> new DateTime('2010-05-12 13:14:45+0300', $tz),
                        'utc' => false], '20100512131445+0300'],
            [['date'=> new DateTime('2010-05-12 13:14:45+0300', $tz),
                        'utc' => true], '20100512101445Z'],
            [['date'=> '2010-05-12 13:14:45+0300',
                        'utc' => false], '20100512131445+0300'],
            [['date'=> '2010-05-12 13:14:45+0300',
                        'utc' => true], '20100512101445Z'],
            [['date'=> DateTime::createFromFormat(DateTime::ISO8601, '2010-05-12T13:14:45+0300'),
                        'utc' => true], '20100512101445Z'],
            [['date'=> DateTime::createFromFormat(DateTime::ISO8601, '2010-05-12T13:14:45+0300'),
                        'utc' => false], '20100512131445+0300'],
            [['date'=> date_timestamp_set(new DateTime(), 0),
                        'utc' => true], '19700101000000Z'],
        ];
    }

    /**
     * @dataProvider toLdapBooleanProvider
     */
    public function testToLdapBoolean($expect, $convert)
    {
        $this->assertEquals($expect, Converter::toldapBoolean($convert));
    }

    public function toLdapBooleanProvider()
    {
        return [
            ['TRUE', true],
            ['TRUE', 1],
            ['TRUE', 'true'],
            ['FALSE', 'false'],
            ['FALSE', false],
            ['FALSE', ['true']],
            ['FALSE', ['false']],
        ];
    }

    /**
     * @dataProvider toLdapSerializeProvider
     */
    public function testToLdapSerialize($expect, $convert)
    {
        $this->assertEquals($expect, Converter::toLdapSerialize($convert));
    }

    public function toLdapSerializeProvider()
    {
        return [
            ['N;', null],
            ['i:1;', 1],
            [serialize(new DateTime('@0')), new DateTime('@0')],
            ['a:3:{i:0;s:4:"test";i:1;i:1;s:3:"foo";s:3:"bar";}', ['test', 1,
                                                                             'foo'=> 'bar']],
        ];
    }

    /**
     * @dataProvider toLdapProvider
     */
    public function testToLdap($expect, $convert)
    {
        $this->assertEquals($expect, Converter::toLdap($convert['value'], $convert['type']));
    }

    public function toLdapProvider()
    {
        return [
            [null, [
                'value' => null,
                'type'  => 0,
            ]],
            ['19700101000000Z', [
                'value'=> 0,
                'type' => 2,
            ]],
            ['0', [
                'value'=> 0,
                'type' => 0,
            ]],
            ['FALSE', [
                'value'=> 0,
                'type' => 1,
            ]],
            ['19700101000000Z', [
                'value'=> DateTime::createFromFormat(DateTime::ISO8601, '1970-01-01T00:00:00+0000'),
                'type' => 0,
            ]],
            [Converter::toldapBoolean(true), [
                'value' => (bool) true,
                'type' => 0
            ]],
            [Converter::toLdapSerialize(new stdClass()), [
                'value' => new stdClass(),
                'type' => 0,
            ]],
            [Converter::toLdapSerialize(['foo']), [
                'value' => ['foo'],
                'type' => 0,
            ]],
            [stream_get_contents(fopen(__FILE__, 'r')), [
                'value' => fopen(__FILE__, 'r'),
                'type' => 0,
            ]],
        ];
    }

    /**
     * @dataProvider fromLdapUnserializeProvider
     */
    public function testFromLdapUnserialize($expect, $convert)
    {
        $this->assertEquals($expect, Converter::fromLdapUnserialize($convert));
    }

    public function testFromLdapUnserializeThrowsException()
    {
        $this->setExpectedException('UnexpectedValueException');
        Converter::fromLdapUnserialize('--');
    }

    public function fromLdapUnserializeProvider()
    {
        return [
            [null, 'N;'],
            [1, 'i:1;'],
            [false, 'b:0;'],
        ];
    }

    public function testFromLdapBoolean()
    {
        $this->assertTrue(Converter::fromLdapBoolean('TRUE'));
        $this->assertFalse(Converter::fromLdapBoolean('FALSE'));
        $this->setExpectedException('InvalidArgumentException');
        Converter::fromLdapBoolean('test');
    }

    /**
     * @dataProvider fromLdapDateTimeProvider
     *
     * @param DateTime $expected
     * @param string   $convert
     * @param  bool  $utc
     * @return void
     */
    public function testFromLdapDateTime($expected, $convert, $utc)
    {
        if (true === $utc) {
            $expected->setTimezone(new DateTimeZone('UTC'));
        }
        $this->assertEquals($expected, Converter::fromLdapDatetime($convert, $utc));
    }

    public function fromLdapDateTimeProvider()
    {
        return [
            [new DateTime('2010-12-24 08:00:23+0300'), '20101224080023+0300', false],
            [new DateTime('2010-12-24 08:00:23+0300'), '20101224080023+03\'00\'', false],
            [new DateTime('2010-12-24 08:00:23+0000'), '20101224080023', false],
            [new DateTime('2010-12-24 08:00:00+0000'), '201012240800', false],
            [new DateTime('2010-12-24 08:00:00+0000'), '2010122408', false],
            [new DateTime('2010-12-24 00:00:00+0000'), '20101224', false],
            [new DateTime('2010-12-01 00:00:00+0000'), '201012', false],
            [new DateTime('2010-01-01 00:00:00+0000'), '2010', false],
            [new DateTime('2010-04-03 12:23:34+0000'), '20100403122334', true],
        ];
    }

    /**
     * @expectedException    InvalidArgumentException
     * @dataProvider         fromLdapDateTimeException
     */
    public function testFromLdapDateTimeThrowsException($value)
    {
        Converter::fromLdapDatetime($value);
    }

    public static function fromLdapDateTimeException()
    {
        return [
            ['foobar'],
            ['201'],
            ['201013'],
            ['20101232'],
            ['2010123124'],
            ['201012312360'],
            ['20101231235960'],
            ['20101231235959+13'],
            ['20101231235959+1160'],
        ];
    }

    /**
     * @dataProvider fromLdapProvider
     */
    public function testFromLdap($expect, $value, $type, $dateTimeAsUtc)
    {
        $this->assertSame($expect, Converter::fromLdap($value, $type, $dateTimeAsUtc));
    }

    public function fromLdapProvider()
    {
        return [
            ['1', '1', 0, true],
            ['0', '0', 0, true],
            [true, 'TRUE', 0, true],
            [false, 'FALSE', 0, true],
            ['123456789', '123456789', 0, true],
            // ZF-11639
            ['+123456789', '+123456789', 0, true],
        ];
    }
}
