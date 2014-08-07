<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper;

/**
 * Tests Zend_View_Helper_ServerUrl
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class ServerSchemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Back up of $_SERVER
     *
     * @var array
     */
    protected $serverBackup;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->serverBackup = $_SERVER;
        unset($_SERVER['HTTPS']);
        unset($_SERVER['SERVER_PORT']);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SERVER = $this->serverBackup;
    }

    public function testConstructorWithOnlyHost()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testConstructorWithOnlyHostIncludingPort()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8000';

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testConstructorWithHostAndHttpsOn()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS']     = 'on';

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('https', $scheme->__invoke());
    }

    public function testConstructorWithHostAndHttpsTrue()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = true;

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('https', $scheme->__invoke());
    }

    public function testConstructorWithHostIncludingPortAndHttpsTrue()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8181';
        $_SERVER['HTTPS'] = true;

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('https', $scheme->__invoke());
    }

    public function testConstructorWithHttpHostIncludingPortAndPortSet()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8181';
        $_SERVER['SERVER_PORT'] = 8181;

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testConstructorWithHttpHostAndServerNameAndPortSet()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_NAME'] = 'example.org';
        $_SERVER['SERVER_PORT'] = 8080;

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testConstructorWithNoHttpHostButServerNameAndPortSet()
    {
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.org';
        $_SERVER['SERVER_PORT'] = 8080;

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testServerUrlWithTrueParam()
    {
        $_SERVER['HTTPS']       = 'off';
        $_SERVER['HTTP_HOST']   = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke(true));
    }

    public function testServerUrlWithInteger()
    {
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke(1337));
    }

    public function testServerUrlWithObject()
    {
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke(new \stdClass()));
    }

    /**
     * @group ZF-9919
     */
    public function testServerUrlWithScheme()
    {
        $_SERVER['HTTP_SCHEME'] = 'https';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $scheme = new Helper\ServerScheme();
        $this->assertEquals('https', $scheme->__invoke());
    }

    /**
     * @group ZF-9919
     */
    public function testServerUrlWithPort()
    {
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $scheme = new Helper\ServerScheme();
        $this->assertEquals('https', $scheme->__invoke());
    }

    /**
     * @group ZF2-508
     */
    public function testServerUrlWithProxy()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org';
        $scheme = new Helper\ServerScheme();
        $scheme->setUseProxy(true);
        $this->assertEquals('http', $scheme->__invoke());
    }

    /**
     * @group ZF2-508
     */
    public function testServerUrlWithMultipleProxies()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org, www.secondhost.org';
        $scheme = new Helper\ServerScheme();
        $scheme->setUseProxy(true);
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testDoesNotUseProxyByDefault()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org, www.secondhost.org';
        $scheme = new Helper\ServerScheme();
        $this->assertEquals('http', $scheme->__invoke());
    }

    public function testCanUseXForwardedPortIfProvided()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org, www.secondhost.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '8888';
        $scheme = new Helper\ServerScheme();
        $scheme->setUseProxy(true);
        $this->assertEquals('http', $scheme->__invoke());
    }
}
