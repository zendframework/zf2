<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Ldap\Dn;

use Zend\Ldap;

/**
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Dn
 */
class ImplodingTest extends \PHPUnit_Framework_TestCase
{
    public function testDnWithMultiValuedRdnRoundTrip()
    {
        $dn1     = 'cn=Surname\, Firstname+uid=userid,cn=name2,dc=example,dc=org';
        $dnArray = Ldap\Dn::explodeDn($dn1);
        $dn2     = Ldap\Dn::implodeDn($dnArray);
        $this->assertEquals($dn1, $dn2);
    }

    public function testImplodeDn()
    {
        $expected = 'cn=name1,cn=name2,dc=example,dc=org';
        $dnArray  = [
            ["cn" => "name1"],
            ["cn" => "name2"],
            ["dc" => "example"],
            ["dc" => "org"]
        ];
        $dn       = Ldap\Dn::implodeDn($dnArray);
        $this->assertEquals($expected, $dn);

        $dn = Ldap\Dn::implodeDn($dnArray, Ldap\Dn::ATTR_CASEFOLD_UPPER, ';');
        $this->assertEquals('CN=name1;CN=name2;DC=example;DC=org', $dn);
    }

    public function testImplodeDnWithUtf8Characters()
    {
        $expected = 'uid=rogasawara,ou=営業部,o=Airius';
        $dnArray  = [
            ["uid" => "rogasawara"],
            ["ou" => "営業部"],
            ["o" => "Airius"],
        ];
        $dn       = Ldap\Dn::implodeDn($dnArray);
        $this->assertEquals($expected, $dn);
    }

    public function testImplodeRdn()
    {
        $a        = ['cn' => 'value'];
        $expected = 'cn=value';
        $this->assertEquals($expected, Ldap\Dn::implodeRdn($a));
    }

    public function testImplodeRdnMultiValuedRdn()
    {
        $a        = ['cn'  => 'value',
                          'uid' => 'testUser'];
        $expected = 'cn=value+uid=testUser';
        $this->assertEquals($expected, Ldap\Dn::implodeRdn($a));
    }

    public function testImplodeRdnMultiValuedRdn2()
    {
        $a        = ['cn'  => 'value',
                          'uid' => 'testUser',
                          'ou'  => 'myDep'];
        $expected = 'cn=value+ou=myDep+uid=testUser';
        $this->assertEquals($expected, Ldap\Dn::implodeRdn($a));
    }

    public function testImplodeRdnCaseFold()
    {
        $a        = ['cn' => 'value'];
        $expected = 'CN=value';
        $this->assertEquals($expected,
            Ldap\Dn::implodeRdn($a, Ldap\Dn::ATTR_CASEFOLD_UPPER)
        );
        $a        = ['CN' => 'value'];
        $expected = 'cn=value';
        $this->assertEquals($expected,
            Ldap\Dn::implodeRdn($a, Ldap\Dn::ATTR_CASEFOLD_LOWER)
        );
    }

    public function testImplodeRdnMultiValuedRdnCaseFold()
    {
        $a        = ['cn'  => 'value',
                          'uid' => 'testUser',
                          'ou'  => 'myDep'];
        $expected = 'CN=value+OU=myDep+UID=testUser';
        $this->assertEquals($expected,
            Ldap\Dn::implodeRdn($a, Ldap\Dn::ATTR_CASEFOLD_UPPER)
        );
        $a        = ['CN'  => 'value',
                          'uID' => 'testUser',
                          'ou'  => 'myDep'];
        $expected = 'cn=value+ou=myDep+uid=testUser';
        $this->assertEquals($expected,
            Ldap\Dn::implodeRdn($a, Ldap\Dn::ATTR_CASEFOLD_LOWER)
        );
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testImplodeRdnInvalidOne()
    {
        $a = ['cn'];
        Ldap\Dn::implodeRdn($a);
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testImplodeRdnInvalidThree()
    {
        $a = ['cn' => 'value', 'ou'];
        Ldap\Dn::implodeRdn($a);
    }
}
