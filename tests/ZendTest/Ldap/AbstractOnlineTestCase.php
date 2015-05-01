<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Ldap;

use Zend\Ldap;

/**
 * @group      Zend_Ldap
 */
abstract class AbstractOnlineTestCase extends AbstractTestCase
{
    /**
     * @var Ldap\Ldap
     */
    private $ldap;

    /**
     * @var array
     */
    private $nodes;

    /**
     * @return Ldap\Ldap
     */
    protected function getLDAP()
    {
        return $this->ldap;
    }

    protected function setUp()
    {
        if (!getenv('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            $this->markTestSkipped("Zend_Ldap online tests are not enabled");
        }

        $options = array(
            'host'     => getenv('TESTS_ZEND_LDAP_HOST'),
            'username' => getenv('TESTS_ZEND_LDAP_USERNAME'),
            'password' => getenv('TESTS_ZEND_LDAP_PASSWORD'),
            'baseDn'   => getenv('TESTS_ZEND_LDAP_WRITEABLE_SUBTREE'),
        );
        if (getenv('TESTS_ZEND_LDAP_PORT') && getenv('TESTS_ZEND_LDAP_PORT') != 389) {
            $options['port'] = getenv('TESTS_ZEND_LDAP_PORT');
        }
        if (getenv('TESTS_ZEND_LDAP_USE_START_TLS')) {
            $options['useStartTls'] = getenv('TESTS_ZEND_LDAP_USE_START_TLS');
        }
        if (getenv('TESTS_ZEND_LDAP_USE_SSL')) {
            $options['useSsl'] = getenv('TESTS_ZEND_LDAP_USE_SSL');
        }
        if (getenv('TESTS_ZEND_LDAP_BIND_REQUIRES_DN')) {
            $options['bindRequiresDn'] = getenv('TESTS_ZEND_LDAP_BIND_REQUIRES_DN');
        }
        if (getenv('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT')) {
            $options['accountFilterFormat'] = getenv('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT');
        }
        if (getenv('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME')) {
            $options['accountDomainName'] = getenv('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME');
        }
        if (getenv('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT')) {
            $options['accountDomainNameShort'] = getenv('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT');
        }

        $this->ldap = new Ldap\Ldap($options);
        $this->ldap->bind();
    }

    protected function tearDown()
    {
        if ($this->ldap !== null) {
            $this->ldap->disconnect();
            $this->ldap = null;
        }
    }

    protected function createDn($dn)
    {
        if (substr($dn, -1) !== ',') {
            $dn .= ',';
        }
        $dn = $dn . getenv('TESTS_ZEND_LDAP_WRITEABLE_SUBTREE');

        return Ldap\Dn::fromString($dn)->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
    }

    protected function prepareLDAPServer()
    {
        $this->nodes = array(
            $this->createDn('ou=Node,')          =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Node",
                  "postalCode"  => "1234"),
            $this->createDn('ou=Test1,ou=Node,') =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test1"),
            $this->createDn('ou=Test2,ou=Node,') =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test2"),
            $this->createDn('ou=Test1,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test1",
                  "l"           => "e"),
            $this->createDn('ou=Test2,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test2",
                  "l"           => "d"),
            $this->createDn('ou=Test3,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test3",
                  "l"           => "c"),
            $this->createDn('ou=Test4,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test4",
                  "l"           => "b"),
            $this->createDn('ou=Test5,')         =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Test5",
                  "l"           => "a"),
        );

        $ldap = $this->ldap->getResource();
        foreach ($this->nodes as $dn => $entry) {
            ldap_add($ldap, $dn, $entry);
        }
    }

    protected function cleanupLDAPServer()
    {
        if (!getenv('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            return;
        }
        $ldap = $this->ldap->getResource();
        foreach (array_reverse($this->nodes) as $dn => $entry) {
            ldap_delete($ldap, $dn);
        }
    }
}
