<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Uri;

use Zend\Uri\File as FileUri;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group      Zend_Uri
 * @group      Zend_Uri_Http
 * @group      Zend_Http
 */
class FileTest extends TestCase
{
    /**
     * Data Providers
     */

    /**
     * Valid schemes
     *
     * @return array
     */
    public static function validSchemeProvider()
    {
        return [
            ['file'],
            ['FILE'],
            ['File'],
        ];
    }

    /**
     * Invalid schemes
     *
     * @return array
     */
    public static function invalidSchemeProvider()
    {
        return [
            ['mailto'],
            ['http'],
            ['g'],
            ['file:']
        ];
    }

    public static function invalidUris()
    {
        return [
            ['file:foo.bar/baz?bat=boo'],
            ['file://foo.bar:80/baz?bat=boo'],
            ['file://user:pass@foo.bar:80/baz?bat=boo'],
            ['file:///baz?bat=boo'],
        ];
    }

    public static function validUris()
    {
        return [
            ['file:///baz'],
            ['file://example.com/baz'],
            ['file://example.com:2132/baz'],
            ['file://example.com:2132/baz#fragment'],
            ['file://user:info@example.com:2132/baz'],
            ['file://C:/foo bar/baz'],
        ];
    }

    public static function unixUris()
    {
        return [
            ['/foo/bar/baz.bat', '/foo/bar/baz.bat'],
            ['/foo/bar/../baz.bat', '/foo/baz.bat'],
            ['/foo/bar/../../baz.bat', '/baz.bat'],
            ['/foo/bar baz.bat', '/foo/bar%20baz.bat'],
        ];
    }

    public static function windowsUris()
    {
        return [
            ['C:\Program Files\Zend Framework\README', 'C:/Program%20Files/Zend%20Framework/README'],
        ];
    }

    /**
     * Tests
     */

    /**
     * Test that specific schemes are valid for this class
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testValidScheme($scheme)
    {
        $uri = new FileUri;
        $uri->setScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
    }

    /**
     * Test that specific schemes are invalid for this class
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testInvalidScheme($scheme)
    {
        $uri = new FileUri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setScheme($scheme);
    }

    /**
     * Test that validateScheme returns false for schemes not valid for use
     * with the File class
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testValidateSchemeInvalid($scheme)
    {
        $this->assertFalse(FileUri::validateScheme($scheme));
    }

    /**
     * @dataProvider invalidUris
     */
    public function testInvalidFileUris($uri)
    {
        $uri = new FileUri($uri);
        $parts = [
            'scheme'    => $uri->getScheme(),
            'user_info' => $uri->getUserInfo(),
            'host'      => $uri->getHost(),
            'port'      => $uri->getPort(),
            'path'      => $uri->getPath(),
            'query'     => $uri->getQueryAsArray(),
            'fragment'  => $uri->getFragment(),
        ];
        $this->assertFalse($uri->isValid(), var_export($parts, 1));
    }

    /**
     * @dataProvider validUris
     */
    public function testValidFileUris($uri)
    {
        $uri = new FileUri($uri);
        $parts = [
            'scheme'    => $uri->getScheme(),
            'user_info' => $uri->getUserInfo(),
            'host'      => $uri->getHost(),
            'port'      => $uri->getPort(),
            'path'      => $uri->getPath(),
            'query'     => $uri->getQueryAsArray(),
            'fragment'  => $uri->getFragment(),
        ];
        $this->assertTrue($uri->isValid(), var_export($parts, 1));
    }

    public function testUserInfoIsAlwaysNull()
    {
        $uri = new FileUri('file://user:pass@host/foo/bar');
        $this->assertNull($uri->getUserInfo());
    }

    public function testFragmentIsAlwaysNull()
    {
        $uri = new FileUri('file:///foo/bar#fragment');
        $this->assertNull($uri->getFragment());
    }

    /**
     * @dataProvider unixUris
     */
    public function testCanCreateUriObjectFromUnixPath($path, $expected)
    {
        $uri = FileUri::fromUnixPath($path);
        $uri->normalize();
        $this->assertEquals($expected, $uri->getPath());
    }

    /**
     * @dataProvider windowsUris
     */
    public function testCanCreateUriObjectFromWindowsPath($path, $expected)
    {
        $uri = FileUri::fromWindowsPath($path);
        $uri->normalize();
        $this->assertEquals($expected, $uri->getPath());
    }
}
