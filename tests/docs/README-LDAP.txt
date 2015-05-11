README
======

LDAP is a strange system.  That said, here are some interesting notes to
help get testing underway.


On Mac OS.X
-----------

Mac OS X comes with openldap installed.  With this, you will need to make
a few changes.  First, the slapd.conf needs to be altered:

    database        bdb
    suffix          "dc=example,dc=com"
    rootdn          "cn=Manager,dc=example,dc=com"
    rootpw          {SSHA}cR8cMV8LTzDpSiInDERB89QnEqpzwzS5

That contains the hashed password for 'insecure'.

Make sure the daemon is running, 

Next create the structure that is needed.

Creating top level node, as well as entry for Manager

    File: example.com.ldif

        dn: dc=example,dc=com
        dc: example
        description: LDAP Example
        objectClass: dcObject
        objectClass: organization
        o: example
    
    Add this:

        ldapadd -x -D "cn=Manager,dc=example,dc=com" -W -f ./example.com.ldif
    
    File: manager.example.com.ldif

        dn: cn=Manager,dc=example,dc=com
        cn: Manager
        objectClass: organizationalRole
    
    Add this:

        ldapadd -x -D "cn=Manager,dc=example,dc=com" -W -f ./manager.example.com.ldif
        
After this has been added, we can then use something like Apache Studio
for LDAP to handle creating the rest of the required information.

Create the following:
    ou=test,dc=example,dc=com
        objectClass=organizationalUnit
        ou=test

also:
    uid=user1,dc=example,dc=com
        objectClass=account
        objectClass=simpleSecurityObject
        uid=user1
        userPassword=<<will be provided>>


phpunit.xml values:

    <const name="TESTS_ZEND_LDAP_HOST" value="localhost"/>
    //<const name="TESTS_ZEND_LDAP_PORT" value="389"/>
    <const name="TESTS_ZEND_LDAP_USE_START_TLS" value="false"/>
    //<const name="TESTS_ZEND_LDAP_USE_SSL" value="false"/>
    <const name="TESTS_ZEND_LDAP_USERNAME" value="cn=Manager,dc=example,dc=com"/>
    <const name="TESTS_ZEND_LDAP_PRINCIPAL_NAME" value="Manager@example.com"/>
    <const name="TESTS_ZEND_LDAP_PASSWORD" value="insecure"/>
    <const name="TESTS_ZEND_LDAP_BIND_REQUIRES_DN" value="true"/>
    <const name="TESTS_ZEND_LDAP_BASE_DN" value="dc=example,dc=com"/>
    <const name="TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT" value="(&(objectClass=account)(uid=%s))"/>
    <const name="TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME" value="example.com"/>
    <const name="TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT" value="EXAMPLE"/>
    <const name="TESTS_ZEND_LDAP_ALT_USERNAME" value="user1"/>
    <const name="TESTS_ZEND_LDAP_ALT_DN" value="uid=user1,dc=example,dc=com"/>
    <const name="TESTS_ZEND_LDAP_ALT_PASSWORD" value="user1"/>
    <const name="TESTS_ZEND_LDAP_WRITEABLE_SUBTREE" value="ou=test,dc=example,dc=com"/>
