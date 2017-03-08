<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text
 */

namespace ZendTest\Text;

use Zend\Text\UniDecoder\UniDecoder;

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @group      Zend_Text
 */
class UniDecoderTest extends \PHPUnit_Framework_TestCase
{
    public function testDryRun()
    {
        $this->assertEquals('', UniDecoder::decode(''));
        $this->assertEquals(' ', UniDecoder::decode(' '));
        $this->assertEquals(null, UniDecoder::decode(null));
    }

    public function testWhitespace()
    {
        $this->assertEquals("\t", UniDecoder::decode("\t"));
        $this->assertEquals("\n", UniDecoder::decode("\n"));
        $this->assertEquals("  ", UniDecoder::decode("  "));
        $this->assertEquals(" \n ", UniDecoder::decode(" \n "));
        $this->assertEquals("\n", UniDecoder::decode("\xE2\x80\xA8"), 'UTF-8 LINE SEPARATOR (U+2028)');
        $this->assertEquals(" ",  UniDecoder::decode("\xE2\x80\x89"), 'UTF-8 THIN SPACE (U+2009)');
    }

    /**
     * @dataProvider lowerAsciiProvider
     */
    public function testLowerBasicAscii($original, $expected)
    {
        $this->assertEquals($expected, UniDecoder::decode($original));
    }

    /**
     * @dataProvider accentsProvider
     */
    public function testChars($original, $expected)
    {
        $this->assertEquals($expected, UniDecoder::decode($original));
    }

    /**
     * @dataProvider greetingsProvider
     */
    public function testStrings($original, $expected)
    {
        $this->assertEquals($expected, UniDecoder::decode($original));
    }

    /**
     * @dataProvider greetingsProvider
     */
    public function testStringsWithWhitespace($original, $expected)
    {
        $this->assertEquals("       $expected      ",  UniDecoder::decode("       $original      "  ));
        $this->assertEquals("\t\t\n$expected\n\t\t\r", UniDecoder::decode("\t\t\n$original\n\t\t\r" ));
    }

    public function testStringWithUtf8Bom()
    {
        $BOM = "\xef\xbb\xbf";
        $this->assertEquals('zazolc', UniDecoder::decode($BOM . 'zażółć'));
        $this->assertEquals('  zazolc', UniDecoder::decode($BOM . '  zażółć'));
        $this->assertEquals("\n\n\nzazolc", UniDecoder::decode($BOM . "\n\n\nzażółć"));
    }

    public function testBrokenUtf8()
    {
        $this->assertEquals('',  UniDecoder::decode("\xc3"),         'Malformed UTF-8 character');
        $this->assertEquals('',  UniDecoder::decode("\xc4"),         'Malformed UTF-8 character');
        $this->assertEquals('',  UniDecoder::decode("\xc5"),         'Malformed UTF-8 character');
        $this->assertEquals('',  UniDecoder::decode("\xa0\xa1"),     'Invalid Sequence Identifier');
        $this->assertEquals('(', UniDecoder::decode("\xe2\x28\xa1"), 'Invalid 3 Octet Sequence (in 2nd Octet)');
        $this->assertEquals(
            'zazolcgesla',
            UniDecoder::decode("zażółć\xc4gęślą"),
            'Malformed UTF-8 character among valid ones'
        );
        $this->assertEquals(
            'zazolcgesla',
            UniDecoder::decode("zażółć\xa0\xa1gęślą"),
            'Invalid Sequence Identifier inside valid UTF-8 string'
        );
        $this->assertEquals(
            'zazolc(gesla',
            UniDecoder::decode("zażółć\xe2\x28\xa1gęślą"),
            'Invalid 3 Octet Sequence inside valid UTF-8 string'
        );


        // The result of double-encoding in this case will be a garbled string that will still be processed by PREG
        // UTF-8 engine, and will be interpreted as something like "zaÅ¼Ã³ÅÄ". This is a valid subject to
        // transliteration and we can retrieve its result.
        $this->assertEquals('zaA1/4A3AA', UniDecoder::decode(utf8_encode('zażółć')), 'Double-encoded UTF-8');

        // The result of decoding will be "za?\xf3??'. The whole string is damaged too much to be processed by PREG
        // engine, but it will be split as an ASCII sequence. The "\xf3" upper-ASCII part will be captured as a
        // sequence identifier but it is not followed by a proper 3 octet sequence, so it will be discarded.
        $this->assertEquals('za???', UniDecoder::decode(utf8_decode('zażółć')), 'Decoded UTF-8');
    }

    /**
     * UTF-8 encoded greetings in several languages.
     *
     * @link http://www.wikihow.com/Say-Hello-in-Different-Languages
     */
    public static function greetingsProvider()
    {
        return array(
            'Belarusian' => array('pryvitańnie',         'pryvitannie'),
            'Estonian'   => array('tere päevast"',       'tere paevast"'),
            'Finnish'    => array('hyvää päivää',        'hyvaa paivaa'),
            'Greek'      => array('Γεια σου',            'Geia sou'),
            'Hindi'      => array('नमस्ते',               'nmste'),
            'Japanese'   => array('おはよう',             'ohayou'),
            'Polish'     => array('dzień dobry, cześć',  'dzien dobry, czesc'),
            'Russian'    => array('привет, здравствуйте','privet, zdravstvuite'),
            'Slovenian'  => array('živjo',               'zivjo'),
        );
    }

    /**
     * UTF-8 encoded letters with multiple international accents
     */
    public static function accentsProvider()
    {
        return array(
            array('À', 'A'),
            array('Á', 'A'),
            array('Â', 'A'),
            array('Ã', 'A'),
            array('Ä', 'A'),
            array('Å', 'A'),
            array('Æ', 'AE'),
            array('Ç', 'C'),
            array('È', 'E'),
            array('É', 'E'),
            array('Ê', 'E'),
            array('Ë', 'E'),
            array('Ì', 'I'),
            array('Í', 'I'),
            array('Î', 'I'),
            array('Ï', 'I'),
            array('Ð', 'D'),
            array('Ñ', 'N'),
            array('Ò', 'O'),
            array('Ó', 'O'),
            array('Ô', 'O'),
            array('Õ', 'O'),
            array('Ö', 'O'),
            array('×', 'x'),
            array('Ø', 'O'),
            array('Ù', 'U'),
            array('Ú', 'U'),
            array('Û', 'U'),
            array('Ü', 'U'),
            array('Ý', 'Y'),
            array('Þ', 'Th'),
            array('ß', 'ss'),
            array('à', 'a'),
            array('á', 'a'),
            array('â', 'a'),
            array('ã', 'a'),
            array('ä', 'a'),
            array('å', 'a'),
            array('æ', 'ae'),
            array('ç', 'c'),
            array('è', 'e'),
            array('é', 'e'),
            array('ê', 'e'),
            array('ë', 'e'),
            array('ì', 'i'),
            array('í', 'i'),
            array('î', 'i'),
            array('ï', 'i'),
            array('ð', 'd'),
            array('ñ', 'n'),
            array('ò', 'o'),
            array('ó', 'o'),
            array('ô', 'o'),
            array('õ', 'o'),
            array('ö', 'o')
        );
    }

    /**
     * @link http://en.wikipedia.org/wiki/Byte_order_mark
     */
    public function BOMProvider()
    {
        return array(
            'UTF-8'     => array("\xef\xbb\xbf"),
            'UTF-16 BE' => array("\xfe\xff"),
            'UTF-16 LE' => array("\xff\xfe"),
            'UTF-32 BE' => array("\x00\x00\xfe\xff"),
            'UTF-32 LE' => array("\xff\xfe\x00\x00"),
        );
    }

    public function lowerAsciiProvider()
    {
        $result = array();
        for($x=0;$x<=80;$x++){
            $result['ASCII '.$x] = array(chr($x),chr($x));
        }
        return $result;
    }


}
