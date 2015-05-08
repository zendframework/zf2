<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Ldap\Ldif;

use Zend\Ldap\Ldif;
use ZendTest\Ldap as TestLdap;

/**
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Ldif
 */
class SimpleEncoderTest extends TestLdap\AbstractTestCase
{
    public static function stringEncodingProvider()
    {
        $testData = [
            ['cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com',
                  'cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com'],
            ['Babs is a big sailing fan, and travels extensively in search of perfect sailing conditions.',
                  'Babs is a big sailing fan, and travels extensively in search of perfect sailing conditions.'],
            ["\x00 NULL CHAR first", base64_encode("\x00 NULL CHAR first")],
            ["\n LF CHAR first", base64_encode("\n LF CHAR first")],
            ["\r CR CHAR first", base64_encode("\r CR CHAR first")],
            [' SPACE CHAR first', base64_encode(' SPACE CHAR first')],
            [': colon CHAR first', base64_encode(': colon CHAR first')],
            ['< less-than CHAR first', base64_encode('< less-than CHAR first')],
            ["\x7f CHR(127) first", base64_encode("\x7f CHR(127) first")],
            ["NULL CHAR \x00 in string", base64_encode("NULL CHAR \x00 in string")],
            ["LF CHAR \n in string", base64_encode("LF CHAR \n in string")],
            ["CR CHAR \r in string", base64_encode("CR CHAR \r in string")],
            ["CHR(127) \x7f in string", base64_encode("CHR(127) \x7f in string")],
            ['Ä first', base64_encode('Ä first')],
            ['in Ä string', base64_encode('in Ä string')],
            ['last char is a string ', base64_encode('last char is a string ')]
        ];
        return $testData;
    }

    /**
     * @dataProvider stringEncodingProvider
     */
    public function testStringEncoding($string, $expected)
    {
        $this->assertEquals($expected, Ldif\Encoder::encode($string));
    }

    public static function attributeEncodingProvider()
    {
        $testData = [
            [['dn' => 'cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com'],
                  'dn: cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com'],
            [['dn' => 'cn=Jürgen Österreicher, ou=Äpfel, dc=airius, dc=com'],
                  'dn:: ' . base64_encode('cn=Jürgen Österreicher, ou=Äpfel, dc=airius, dc=com')],
            [['description' => 'Babs is a big sailing fan, and travels extensively in search of perfect sailing conditions.'],
                  'description: Babs is a big sailing fan, and travels extensively in search of p'
                      . PHP_EOL . ' erfect sailing conditions.'],
            [['description' => "CHR(127) \x7f in string"],
                  'description:: ' . base64_encode("CHR(127) \x7f in string")],
            [['description' => '1234567890123456789012345678901234567890123456789012345678901234 567890'],
                  'description: 1234567890123456789012345678901234567890123456789012345678901234 ' . PHP_EOL
                      . ' 567890'],
        ];
        return $testData;
    }

    /**
     * @dataProvider attributeEncodingProvider
     */
    public function testAttributeEncoding($array, $expected)
    {
        $actual = Ldif\Encoder::encode($array);
        $this->assertEquals($expected, $actual);
    }

    public function testChangedWrapCount()
    {
        $input    = '56789012345678901234567890';
        $expected = 'dn: 567890' . PHP_EOL . ' 1234567890' . PHP_EOL . ' 1234567890';
        $output   = Ldif\Encoder::encode(['dn' => $input], ['wrap' => 10]);
        $this->assertEquals($expected, $output);
    }

    public function testEncodeMultipleAttributes()
    {
        $data     = [
            'a' => ['a', 'b'],
            'b' => 'c',
            'c' => '',
            'd' => [],
            'e' => ['']];
        $expected = 'a: a' . PHP_EOL .
            'a: b' . PHP_EOL .
            'b: c' . PHP_EOL .
            'c: ' . PHP_EOL .
            'd: ' . PHP_EOL .
            'e: ';
        $actual   = Ldif\Encoder::encode($data);
        $this->assertEquals($expected, $actual);
    }

    public function testEncodeUnsupportedType()
    {
        $this->assertNull(Ldif\Encoder::encode(new \stdClass()));
    }

    public function testSorting()
    {
        $data     = [
            'cn'          => ['name'],
            'dn'          => 'cn=name,dc=example,dc=org',
            'host'        => ['a', 'b', 'c'],
            'empty'       => [],
            'boolean'     => ['TRUE', 'FALSE'],
            'objectclass' => ['account', 'top'],
        ];
        $expected = 'version: 1' . PHP_EOL .
            'dn: cn=name,dc=example,dc=org' . PHP_EOL .
            'objectclass: account' . PHP_EOL .
            'objectclass: top' . PHP_EOL .
            'boolean: TRUE' . PHP_EOL .
            'boolean: FALSE' . PHP_EOL .
            'cn: name' . PHP_EOL .
            'empty: ' . PHP_EOL .
            'host: a' . PHP_EOL .
            'host: b' . PHP_EOL .
            'host: c';
        $actual   = Ldif\Encoder::encode($data);
        $this->assertEquals($expected, $actual);

        $expected = 'version: 1' . PHP_EOL .
            'cn: name' . PHP_EOL .
            'dn: cn=name,dc=example,dc=org' . PHP_EOL .
            'host: a' . PHP_EOL .
            'host: b' . PHP_EOL .
            'host: c' . PHP_EOL .
            'empty: ' . PHP_EOL .
            'boolean: TRUE' . PHP_EOL .
            'boolean: FALSE' . PHP_EOL .
            'objectclass: account' . PHP_EOL .
            'objectclass: top';
        $actual   = Ldif\Encoder::encode($data, ['sort' => false]);
        $this->assertEquals($expected, $actual);
    }

    public function testNodeEncoding()
    {
        $node     = $this->createTestNode();
        $expected = 'version: 1' . PHP_EOL .
            'dn: cn=name,dc=example,dc=org' . PHP_EOL .
            'objectclass: account' . PHP_EOL .
            'objectclass: top' . PHP_EOL .
            'boolean: TRUE' . PHP_EOL .
            'boolean: FALSE' . PHP_EOL .
            'cn: name' . PHP_EOL .
            'empty: ' . PHP_EOL .
            'host: a' . PHP_EOL .
            'host: b' . PHP_EOL .
            'host: c';
        $actual   = $node->toLdif();
        $this->assertEquals($expected, $actual);

        $actual = Ldif\Encoder::encode($node);
        $this->assertEquals($expected, $actual);
    }

    public function testSupressVersionHeader()
    {
        $data     = [
            'cn'          => ['name'],
            'dn'          => 'cn=name,dc=example,dc=org',
            'host'        => ['a', 'b', 'c'],
            'empty'       => [],
            'boolean'     => ['TRUE', 'FALSE'],
            'objectclass' => ['account', 'top'],
        ];
        $expected = 'dn: cn=name,dc=example,dc=org' . PHP_EOL .
            'objectclass: account' . PHP_EOL .
            'objectclass: top' . PHP_EOL .
            'boolean: TRUE' . PHP_EOL .
            'boolean: FALSE' . PHP_EOL .
            'cn: name' . PHP_EOL .
            'empty: ' . PHP_EOL .
            'host: a' . PHP_EOL .
            'host: b' . PHP_EOL .
            'host: c';
        $actual   = Ldif\Encoder::encode($data, ['version' => null]);
        $this->assertEquals($expected, $actual);
    }

    public function testEncodingWithJapaneseCharacters()
    {
        $data     = [
            'dn'                         => 'uid=rogasawara,ou=営業部,o=Airius',
            'objectclass'                => ['top', 'person', 'organizationalPerson', 'inetOrgPerson'],
            'uid'                        => ['rogasawara'],
            'mail'                       => ['rogasawara@airius.co.jp'],
            'givenname;lang-ja'          => ['ロドニー'],
            'sn;lang-ja'                 => ['小笠原'],
            'cn;lang-ja'                 => ['小笠原 ロドニー'],
            'title;lang-ja'              => ['営業部 部長'],
            'preferredlanguage'          => ['ja'],
            'givenname'                  => ['ロドニー'],
            'sn'                         => ['小笠原'],
            'cn'                         => ['小笠原 ロドニー'],
            'title'                      => ['営業部 部長'],
            'givenname;lang-ja;phonetic' => ['ろどにー'],
            'sn;lang-ja;phonetic'        => ['おがさわら'],
            'cn;lang-ja;phonetic'        => ['おがさわら ろどにー'],
            'title;lang-ja;phonetic'     => ['えいぎょうぶ ぶちょう'],
            'givenname;lang-en'          => ['Rodney'],
            'sn;lang-en'                 => ['Ogasawara'],
            'cn;lang-en'                 => ['Rodney Ogasawara'],
            'title;lang-en'              => ['Sales, Director'],
        ];
        $expected = 'dn:: dWlkPXJvZ2FzYXdhcmEsb3U95Za25qWt6YOoLG89QWlyaXVz' . PHP_EOL .
            'objectclass: top' . PHP_EOL .
            'objectclass: person' . PHP_EOL .
            'objectclass: organizationalPerson' . PHP_EOL .
            'objectclass: inetOrgPerson' . PHP_EOL .
            'uid: rogasawara' . PHP_EOL .
            'mail: rogasawara@airius.co.jp' . PHP_EOL .
            'givenname;lang-ja:: 44Ot44OJ44OL44O8' . PHP_EOL .
            'sn;lang-ja:: 5bCP56yg5Y6f' . PHP_EOL .
            'cn;lang-ja:: 5bCP56yg5Y6fIOODreODieODi+ODvA==' . PHP_EOL .
            'title;lang-ja:: 5Za25qWt6YOoIOmDqOmVtw==' . PHP_EOL .
            'preferredlanguage: ja' . PHP_EOL .
            'givenname:: 44Ot44OJ44OL44O8' . PHP_EOL .
            'sn:: 5bCP56yg5Y6f' . PHP_EOL .
            'cn:: 5bCP56yg5Y6fIOODreODieODi+ODvA==' . PHP_EOL .
            'title:: 5Za25qWt6YOoIOmDqOmVtw==' . PHP_EOL .
            'givenname;lang-ja;phonetic:: 44KN44Gp44Gr44O8' . PHP_EOL .
            'sn;lang-ja;phonetic:: 44GK44GM44GV44KP44KJ' . PHP_EOL .
            'cn;lang-ja;phonetic:: 44GK44GM44GV44KP44KJIOOCjeOBqeOBq+ODvA==' . PHP_EOL .
            'title;lang-ja;phonetic:: 44GI44GE44GO44KH44GG44G2IOOBtuOBoeOCh+OBhg==' . PHP_EOL .
            'givenname;lang-en: Rodney' . PHP_EOL .
            'sn;lang-en: Ogasawara' . PHP_EOL .
            'cn;lang-en: Rodney Ogasawara' . PHP_EOL .
            'title;lang-en: Sales, Director';
        $actual   = Ldif\Encoder::encode($data, ['sort'   => false,
                                                     'version' => null]
        );
        $this->assertEquals($expected, $actual);
    }
}
