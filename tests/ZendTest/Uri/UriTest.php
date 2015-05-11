<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Uri;

use Zend\Uri\Uri;

/**
 * @group      Zend_Uri
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * General composing / parsing tests
     */

    /**
     * Test that parsing and composing a valid URI returns the same URI
     *
     * @param        string $uriString
     * @dataProvider validUriStringProvider
     */
    public function testParseComposeUri($uriString)
    {
        $uri = new Uri($uriString);
        $this->assertEquals($uriString, $uri->toString());
    }

    /**
     * Test composing a new URI by setting the different URI parts programatically.
     *
     * Also tests casting a URI object to string.
     *
     * @param string $exp
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testComposeNewUriAndCastToString($exp, $parts)
    {
        $uri = new Uri;
        foreach ($parts as $k => $v) {
            $setMethod = 'set' . ucfirst($k);
            $uri->$setMethod($v);
        }

        $this->assertEquals($exp, (string) $uri);
    }

    /**
     * Test the parseScheme static method to extract the scheme part
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testParseScheme($uriString, $parts)
    {
        $scheme = Uri::parseScheme($uriString);
        if (! isset($parts['scheme'])) {
            $parts['scheme'] = null;
        }

        $this->assertEquals($parts['scheme'], $scheme);
    }

    /**
     * Test that parseScheme throws an exception in case of invalid input
     *
     * @param  mixed $input
     * @dataProvider notStringInputProvider
     */
    public function testParseSchemeInvalidInput($input)
    {
        $this->setExpectedException('Zend\Uri\Exception\InvalidArgumentException');
        $scheme = Uri::parseScheme($input);
    }

    /**
     * Test that __toString() (magic) returns an empty string if URI is invalid
     *
     * @dataProvider invalidUriObjectProvider
     */
    public function testMagicToStringEmptyIfInvalid(Uri $uri)
    {
        $this->assertEquals('', (string) $uri);
    }

    /**
     * Test that toString() (not magic) throws an exception if URI is invalid
     *
     * @dataProvider invalidUriObjectProvider
     */
    public function testToStringThrowsExceptionIfInvalid(Uri $uri)
    {
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriException');
        $string = $uri->toString();
    }

    /**
     * Test that we can parse a malformed URI
     *
     * @link http://framework.zend.com/issues/browse/ZF-11286
     */
    public function testCanParseMalformedUrlZF11286()
    {
        $urlString = 'http://example.org/SitePages/file has spaces.html?foo=bar';
        $uri = new Uri($urlString);
        $fixedUri = new Uri($uri->toString());

        $this->assertEquals('/SitePages/file%20has%20spaces.html', $fixedUri->getPath());
    }

    /**
     * Accessor Tests
     */

    /**
     * Test that we can get the scheme out of a parsed URI
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetScheme($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['scheme'])) {
            $this->assertEquals($parts['scheme'], $uri->getScheme());
        } else {
            $this->assertNull($uri->getScheme());
        }
    }

    /**
     * Test that we get the correct userInfo
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetUserInfo($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['userInfo'])) {
            $this->assertEquals($parts['userInfo'], $uri->getUserInfo());
        } else {
            $this->assertNull($uri->getUserInfo());
        }
    }

    /**
     * Test that we can get the host out of a parsed URI
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetHost($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['host'])) {
            $this->assertEquals($parts['host'], $uri->getHost());
        } else {
            $this->assertNull($uri->getHost());
        }
    }

    /**
     * Test that we can get the port out of a parsed Uri
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetPort($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['port'])) {
            $this->assertEquals($parts['port'], $uri->getPort());
        } else {
            $this->assertNull($uri->getPort());
        }
    }

    /**
     * Test that we can get the path out of a parsed Uri
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetPath($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['path'])) {
            $this->assertEquals($parts['path'], $uri->getPath());
        } else {
            $this->assertNull($uri->getPath());
        }
    }

    /**
     * Test that we can get the query out of a parsed Uri
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetQuery($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['query'])) {
            $this->assertEquals($parts['query'], $uri->getQuery());
        } else {
            $this->assertNull($uri->getQuery());
        }
    }

    /**
     * @group ZF-1480
     */
    public function testGetQueryAsArrayReturnsCorrectArray()
    {
        $url = new Uri('http://example.com/foo/?test=a&var[]=1&var[]=2&some[thing]=3');
        $this->assertEquals('test=a&var[]=1&var[]=2&some[thing]=3', $url->getQuery());

        $exp = [
            'test' => 'a',
            'var'  => [1, 2],
            'some' => ['thing' => 3]
        ];

        $this->assertEquals($exp, $url->getQueryAsArray());
    }

    /**
     * Test that we can get the fragment out of a parsed URI
     *
     * @param string $uriString
     * @param array  $parts
     * @dataProvider uriWithPartsProvider
     */
    public function testGetFragment($uriString, $parts)
    {
        $uri = new Uri($uriString);
        if (isset($parts['fragment'])) {
            $this->assertEquals($parts['fragment'], $uri->getFragment());
        } else {
            $this->assertNull($uri->getFragment());
        }
    }

    /**
     * Mutator Tests
     */

    /**
     * Test we can set the scheme to NULL
     *
     */
    public function testSetSchemeNull()
    {
        $uri = new Uri('http://example.com');
        $this->assertEquals('http', $uri->getScheme());

        $uri->setScheme(null);
        $this->assertNull($uri->getScheme());
    }

    /**
     * Test we can set different valid schemes
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testSetSchemeValid($scheme)
    {
        $uri = new Uri;
        $uri->setScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
    }

    /**
     * Test that setting an invalid scheme causes an exception
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testSetInvalidScheme($scheme)
    {
        $uri = new Uri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setScheme($scheme);
    }

    /**
     * Test that we can set a valid hostname
     *
     * @param string $host
     * @dataProvider validHostProvider
     */
    public function testSetGetValidHost($host)
    {
        $uri = new Uri;
        $uri->setHost($host);
        $this->assertEquals($host, $uri->getHost());
    }

    /**
     * Test that when setting an invalid host an exception is thrown
     *
     * @param string $host
     * @dataProvider invalidHostProvider
     */
    public function testSetInvalidHost($host)
    {
        $uri = new Uri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setHost($host);
    }

    /**
     * Test that we can set the host part to 'null'
     *
     */
    public function testSetNullHost()
    {
        $uri = new Uri('http://example.com/bar');
        $uri->setHost(null);
        $this->assertNull($uri->getHost());
    }

    /**
     * Test that we can use an array to set the query parameters
     *
     * @param array  $data
     * @param string $expqs
     * @dataProvider queryParamsArrayProvider
     */
    public function testSetQueryFromArray(array $data, $expqs)
    {
        $uri = new Uri();
        $uri->setQuery($data);

        $this->assertEquals('?' . $expqs, $uri->toString());
    }

    /**
     * Validation and encoding tests
     */

    /**
     * Test that valid URIs pass validation
     *
     * @param string $uriString
     * @dataProvider validUriStringProvider
     */
    public function testValidUriIsValid($uriString)
    {
        $uri = new Uri($uriString);
        $this->assertTrue($uri->isValid());
    }

    /**
     * Test that valid relative URIs pass validation
     *
     * @param string $uriString
     * @dataProvider validRelativeUriStringProvider
     */
    public function testValidRelativeUriIsValid($uriString)
    {
        $uri = new Uri($uriString);
        $this->assertTrue($uri->isValidRelative());
    }

    /**
     * Test that invalid URIs fail validation
     *
     * @param \Zend\Uri\Uri $uri
     * @dataProvider invalidUriObjectProvider
     */
    public function testInvalidUriIsInvalid(Uri $uri)
    {
        $this->assertFalse($uri->isValid());
    }

    /**
     * Test that invalid relative URIs fail validation
     *
     * @param \Zend\Uri\Uri $uri
     * @dataProvider invalidRelativeUriObjectProvider
     */
    public function testInvalidRelativeUriIsInvalid(Uri $uri)
    {
        $this->assertFalse($uri->isValidRelative());
    }

    /**
     * Check that valid schemes are valid according to validateScheme()
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testValidateSchemeValid($scheme)
    {
        $this->assertTrue(Uri::validateScheme($scheme));
    }

    /**
     * Check that invalid schemes are invalid according to validateScheme()
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testValidateSchemeInvalid($scheme)
    {
        $this->assertFalse(Uri::validateScheme($scheme));
    }

    /**
     * Check that valid hosts are valid according to validateHost()
     *
     * @param string $host
     * @dataProvider validHostProvider
     */
    public function testValidateHostValid($host)
    {
        $this->assertTrue(Uri::validateHost($host));
    }

    /**
     * Check that invalid hosts are invalid according to validateHost()
     *
     * @param string $host
     * @dataProvider invalidHostProvider
     */
    public function testValidateHostInvalid($host)
    {
        $this->assertFalse(Uri::validateHost($host));
    }

    /**
     * Check that valid paths are valid according to validatePath()
     *
     * @param string $path
     * @dataProvider validPathProvider
     */
    public function testValidatePathValid($path)
    {
        $this->assertTrue(Uri::validatePath($path));
    }

    /**
     * Check that invalid paths are invalid according to validatePath()
     *
     * @param string $path
     * @dataProvider invalidPathProvider
     */
    public function testValidatePathInvalid($path)
    {
        $this->assertFalse(Uri::validatePath($path));
    }

    /**
     * Test that valid path parts are unchanged by the 'encode' function
     *
     * @param string $path
     * @dataProvider validPathProvider
     */
    public function testEncodePathValid($path)
    {
        $this->assertEquals($path, Uri::encodePath($path));
    }

    /**
     * Test that invalid path parts are properly encoded by the 'encode' function
     *
     * @param string $path
     * @param string $exp
     * @dataProvider invalidPathProvider
     */
    public function testEncodePathInvalid($path, $exp)
    {
        $this->assertEquals($exp, Uri::encodePath($path));
    }

    /**
     * Test that valid query or fragment parts are validated properly
     *
     * @param $input
     * @dataProvider validQueryFragmentProvider
     */
    public function testValidQueryFragment($input)
    {
        $this->assertTrue(Uri::validateQueryFragment($input));
    }

    /**
     * Test that invalid query or fragment parts are validated properly
     *
     * @param $input
     * @dataProvider invalidQueryFragmentProvider
     */
    public function testInvalidQueryFragment($input, $exp)
    {
        $this->assertFalse(Uri::validateQueryFragment($input));
    }

    /**
     * Test that valid query or fragment parts properly encoded
     *
     * @param $input
     * @param $exp
     * @dataProvider invalidQueryFragmentProvider
     */
    public function testEncodeInvalidQueryFragment($input, $exp)
    {
        $actual = Uri::encodeQueryFragment($input);
        $this->assertEquals($exp, $actual);
    }

    /**
     * Test that valid query or fragment parts are not modified when paseed
     * through encodeQueryFragment()
     *
     * @param $input
     * @param $exp
     * @dataProvider validQueryFragmentProvider
     */
    public function testEncodeValidQueryFragment($input)
    {
        $actual = Uri::encodeQueryFragment($input);
        $this->assertEquals($input, $actual);
    }

    /**
     * Test that valid userInfo input is validated by validateUserInfo
     *
     * @param string $userInfo
     * @dataProvider validUserInfoProvider
     */
    public function testValidateUserInfoValid($userInfo)
    {
        $this->assertTrue(Uri::validateUserInfo($userInfo));
    }

    /**
     * Test that invalid userInfo input is not accepted by validateUserInfo
     *
     * @param string $userInfo
     * @param string $exp
     * @dataProvider invalidUserInfoProvider
     */
    public function testValidateUserInfoInvalid($userInfo, $exp)
    {
        $this->assertFalse(Uri::validateUserInfo($userInfo));
    }

    /**
     * Test that valid userInfo is returned unchanged by encodeUserInfo
     *
     * @param $userInfo
     * @dataProvider validUserInfoProvider
     */
    public function testEncodeUserInfoValid($userInfo)
    {
        $this->assertEquals($userInfo, Uri::encodeUserInfo($userInfo));
    }

    /**
     * Test that invalid userInfo input properly encoded by encodeUserInfo
     *
     * @param string $userInfo
     * @param string $exp
     * @dataProvider invalidUserInfoProvider
     */
    public function testEncodeUserInfoInvalid($userInfo, $exp)
    {
        $this->assertEquals($exp, Uri::encodeUserInfo($userInfo));
    }

    /**
     * Test that validatePort works for valid ports
     *
     * @param mixed $port
     * @dataProvider validPortProvider
     */
    public function testValidatePortValid($port)
    {
        $this->assertTrue(Uri::validatePort($port));
    }

    /**
     * Test that validatePort works for invalid ports
     *
     * @param mixed $port
     * @dataProvider invalidPortProvider
     */
    public function testValidatePortInvalid($port)
    {
        $this->assertFalse(Uri::validatePort($port));
    }

    /**
     * @group ZF-1480
     */
    /*
    public function testAddReplaceQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $url = new Uri('http://example.com/foo/?a=1&b=2&c=3');
        $url->addReplaceQueryParameters(array('b' => 4, 'd' => -1));
        $this->assertEquals(array(
            'a' => 1,
            'b' => 4,
            'c' => 3,
            'd' => -1
        ), $url->getQueryAsArray());
        $this->assertEquals('a=1&b=4&c=3&d=-1', $url->getQuery());
    }
    */

    /**
     * @group ZF-1480
     */
    /*
    public function testRemoveQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $url = new Uri('http://example.com/foo/?a=1&b=2&c=3&d=4');
        $url->removeQueryParameters(array('b', 'd', 'e'));
        $this->assertEquals(array('a' => 1, 'c' => 3), $url->getQueryAsArray());
        $this->assertEquals('a=1&c=3', $url->getQuery());
    }
    */

    /**
     * Resolving, Normalization and Reference creation tests
     */

    /**
     * Test that resolving relative URIs works using the examples specified in
     * the RFC
     *
     * @param string $relative
     * @param string $expected
     * @dataProvider resolvedAbsoluteUriProvider
     */
    public function testRelativeUriResolvingRfcSamples($baseUrl, $relative, $expected)
    {
        $uri = new Uri($relative);
        $uri->resolve($baseUrl);

        $this->assertEquals($expected, $uri->toString());
    }

    /**
     * Test the removal of extra dot segments from paths
     *
     * @param        $orig
     * @param        $expected
     * @dataProvider pathWithDotSegmentProvider
     */
    public function testRemovePathDotSegments($orig, $expected)
    {
        $this->assertEquals($expected, Uri::removePathDotSegments($orig));
    }

    /**
     * Test normalizing URLs
     *
     * @param string $orig
     * @param string $expected
     * @dataProvider normalizedUrlsProvider
     */
    public function testNormalizeUrl($orig, $expected)
    {
        $url = new Uri($orig);
        $this->assertEquals($expected, $url->normalize()->toString());
    }

    /**
     * Test the merge() static method for merging new URIs
     *
     * @param string $base
     * @param string $relative
     * @param string $expected
     * @dataProvider resolvedAbsoluteUriProvider
     */
    public function testMergeToNewUri($base, $relative, $expected)
    {
        $actual = Uri::merge($base, $relative)->toString();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure that the ::merge() method does not modify any input objects
     *
     * This performs two checks:
     *  1. That the result object is *not* the same object as any of the input ones
     *  2. That the method doesn't modify the input objects
     *
     */
    public function testMergeTwoObjectsNotModifying()
    {
        $base = new Uri('http://example.com/bar');
        $ref  = new Uri('baz?qwe=1');

        $baseSig = serialize($base);
        $refSig  = serialize($ref);

        $actual = Uri::merge($base, $ref);

        $this->assertNotSame($base, $actual);
        $this->assertNotSame($ref, $actual);

        $this->assertEquals($baseSig, serialize($base));
        $this->assertEquals($refSig, serialize($ref));
    }

    /**
     * Test that makeRelative() works as expected
     *
     * @dataProvider commonBaseUriProvider
     */
    public function testMakeRelative($base, $url, $expected)
    {
        $url = new Uri($url);
        $url->makeRelative($base);
        $this->assertEquals($expected, $url->toString());
    }

    /**
     * Other tests
     */

    /**
     * Test that the copy constructor works
     *
     * @dataProvider validUriStringProvider
     */
    public function testConstructorCopyExistingObject($uriString)
    {
        $uri = new Uri($uriString);
        $uri2 = new Uri($uri);

        $this->assertEquals($uri, $uri2);
    }

    /**
     * Test that the constructor throws an exception on invalid input
     *
     * @param mixed $input
     * @dataProvider invalidConstructorInputProvider
     */
    public function testConstructorInvalidInput($input)
    {
        $this->setExpectedException('Zend\Uri\Exception\InvalidArgumentException');
        $uri = new Uri($input);
    }

    /**
     * Test the fluent interface
     *
     * @param string $method
     * @param string $params
     * @dataProvider fluentInterfaceMethodProvider
     */
    public function testFluentInterface($method, $params)
    {
        $uri = new Uri;
        $ret = call_user_func_array([$uri, $method], $params);
        $this->assertSame($uri, $ret);
    }

    /**
     * Data Providers
     */

    public function validUserInfoProvider()
    {
        return [
            ['user:'],
            [':password'],
            ['user:password'],
            [':'],
            ['my-user'],
            ['one:two:three:four'],
            ['my-user-has-%3A-colon:pass'],
            ['a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word']
        ];
    }

    public function invalidUserInfoProvider()
    {
        return [
            ['an`di:password',    'an%60di:password'],
            ['user name',         'user%20name'],
            ['shahar.e@zend.com', 'shahar.e%40zend.com']
        ];
    }

    /**
     * Data provider for valid URIs, not necessarily complete
     *
     * @return array
     */
    public function validUriStringProvider()
    {
        return [
            ['a:b'],
            ['http://www.zend.com'],
            ['https://example.com:10082/foo/bar?query'],
            ['../relative/path'],
            ['?queryOnly'],
            ['#fragmentOnly'],
            ['mailto:bob@example.com'],
            ['bob@example.com'],
            ['http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com'],
            ['http://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:80/index.html'],
            ['http://[1080::8:800:200C:417A]/foo'],
            ['http://[::192.9.5.5]/ipng'],
            ['http://[::FFFF:129.144.52.38]:80/index.html'],
            ['http://[2620:0:1cfe:face:b00c::3]/'],
            ['http://[2010:836B:4179::836B:4179]'],
            ['http'],
            ['www.example.org:80'],
            ['www.example.org'],
            ['http://foo'],
            ['ftp://user:pass@example.org/'],
            ['www.fi/'],
            ['http://1.1.1.1/'],
            ['1.1.1.1'],
            ['1.256.1.1'], // Hostnames can be only numbers
            ['http://[::1]/'],
            ['file:/'], // schema without host
            ['http:///'], // host empty
            ['http:::/foo'], // schema + path
            ['2620:0:1cfe:face:b00c::3'], // Not IPv6, is Path
        ];
    }

    /**
     * Data provider for valid relative URIs, not necessarily complete
     *
     * @return array
     */
    public function validRelativeUriStringProvider()
    {
        return [
            ['foo/bar?query'],
            ['../relative/path'],
            ['?queryOnly'],
            ['#fragmentOnly'],
        ];
    }

    /**
     * Valid schemes
     *
     * @return array
     */
    public function validSchemeProvider()
    {
        return [
            // Valid schemes
            ['http'],
            ['HTTP'],
            ['File'],
            ['h'],
            ['h2'],
            ['a+b'],
            ['k-'],
         ];
    }

    /**
     * Invalid schemes
     *
     * @return array
     */
    public function invalidSchemeProvider()
    {
        return [
            ['ht tp'],
            ['htp_p'],
            ['-tp'],
            ['22c'],
            ['h%acp'],
        ];
    }

    /**
     * Valid query or fragment parts
     *
     * Each valid query or fragment part should require no encoding and if
     * passed throuh an encoding method should return unchanged.
     *
     * @return array
     */
    public function validQueryFragmentProvider()
    {
        return [
            ['a=1&b=2&c=3&d=4'],
            ['with?questionmark/andslash'],
            ['id=123&url=http://example.com/?bar=foo+baz'],
            ['with%20%0Aline%20break'],
        ];
    }

    /**
     * Invalid query or fragment parts.
     *
     * Additionally, this method supplies a valid, URL-encoded representation
     * of each invalid part, which can be used to test encoding.
     *
     * @return array
     */
    public function invalidQueryFragmentProvider()
    {
        return [
            ['with#pound', 'with%23pound'],
            ['with space', 'with%20space'],
            ['test=a&var[]=1&var[]=2&some[thing]=3', 'test=a&var%5B%5D=1&var%5B%5D=2&some%5Bthing%5D=3'],
            ["with \nline break", "with%20%0Aline%20break"],
            ["with%percent", "with%25percent"],
        ];
    }

    /**
     * Data provider for invalid URI objects
     *
     * @return array
     */
    public function invalidUriObjectProvider()
    {
        // Empty URI is not valid
        $obj1 = new Uri;

        // Path cannot begin with '//' if there is no authority part
        $obj2 = new Uri;
        $obj2->setPath('//path');

        // A port-only URI with no host
        $obj3 = new Uri;
        $obj3->setPort(123);

        // A userinfo-only URI with no host
        $obj4 = new Uri;
        $obj4->setUserInfo('shahar:password');

        return [
            [$obj1],
            [$obj2],
            [$obj3],
            [$obj4]
        ];
    }

    /**
     * Data provider for invalid relative URI objects
     *
     * @return array
     */
    public function invalidRelativeUriObjectProvider()
    {
        // Empty URI is not valid
        $obj1 = new Uri;

        // Path cannot begin with '//'
        $obj2 = new Uri;
        $obj2->setPath('//path');

        // An object with port
        $obj3 = new Uri;
        $obj3->setPort(123);

        // An object with userInfo
        $obj4 = new Uri;
        $obj4->setUserInfo('shahar:password');

        // An object with scheme
        $obj5 = new Uri;
        $obj5->setScheme('https');

        // An object with host
        $obj6 = new Uri;
        $obj6->setHost('example.com');

        return [
            [$obj1],
            [$obj2],
            [$obj3],
            [$obj4],
            [$obj5],
            [$obj6]
        ];
    }


    /**
     * Data provider for valid URIs with their different parts
     *
     * @return array
     */
    public function uriWithPartsProvider()
    {
        return [
            ['ht-tp://server/path?query', [
                'scheme'   => 'ht-tp',
                'host'     => 'server',
                'path'     => '/path',
                'query'    => 'query',
            ]],
            ['file:///foo/bar', [
                'scheme'   => 'file',
                'host'     => '',
                'path'     => '/foo/bar',
            ]],
            ['http://dude:lebowski@example.com/#fr/ag?me.nt', [
                'scheme'   => 'http',
                'userInfo' => 'dude:lebowski',
                'host'     => 'example.com',
                'path'     => '/',
                'fragment' => 'fr/ag?me.nt'
            ]],
            ['/relative/path', [
                'path' => '/relative/path'
            ]],
            ['ftp://example.com:5555', [
                'scheme' => 'ftp',
                'host'   => 'example.com',
                'port'   => 5555,
                'path'   => ''
            ]],
            ['http://example.com/foo//bar/baz//fob/', [
                'scheme' => 'http',
                'host'   => 'example.com',
                'path'   => '/foo//bar/baz//fob/'
            ]]
        ];
    }

    /**
     * Provider for valid ports
     *
     * @return array
     */
    public function validPortProvider()
    {
        return [
            [null],
            [1],
            [0xffff],
            [80],
            ['443']
        ];
    }

    /**
     * Provider for invalid ports
     *
     * @return array
     */
    public function invalidPortProvider()
    {
        return [
            [0],
            [-1],
            [0x10000],
            ['foo'],
            ['0xf'],
            ['-'],
            [':'],
            ['/']
        ];
    }

    public function validHostProvider()
    {
        return [
            // IPv4 addresses
            ['10.1.2.3'],
            ['127.0.0.1'],
            ['0.0.0.0'],
            ['255.255.255.255'],

            // IPv6 addresses
            // Examples from http://en.wikipedia.org/wiki/IPv6_address
            ['[2001:0db8:85a3:0000:0000:8a2e:0370:7334]'],
            ['[2001:db8:85a3:0:0:8a2e:370:7334]'],
            ['[2001:db8:85a3::8a2e:370:7334]'],
            ['[0:0:0:0:0:0:0:1]'],
            ['[::1]'],
            ['[2001:0db8:85a3:08d3:1319:8a2e:0370:7348]'],

            // Internet and local DNS names
            ['www.example.com'],
            ['zend.com'],
            ['php-israel.org'],
            ['arr.gr'],
            ['localhost'],
            ['loca.host'],
            ['zend-framework.test'],
            ['a.b.c.d'],
            ['a1.b2.c3.d4'],
            ['some-domain-with-dashes'],

            // Registered name (other than DNS names)
            ['some~unre_served.ch4r5'],
            ['pct.%D7%A9%D7%97%D7%A8%20%D7%94%D7%92%D7%93%D7%95%D7%9C.co.il'],
            ['sub-delims-!$&\'()*+,;=.are.ok'],
            ['%2F%3A']
        ];
    }

    public function invalidHostProvider()
    {
        return [
            ['with space'],
            ['[]'],
            ['[12:34'],
        ];
    }

    public function validPathProvider()
    {
        return [
            [''],
            ['/'],
            [':'],
            ['/foo/bar'],
            ['foo/bar'],
            ['/foo;arg2=1&arg2=2/bar;baz/bla'],
            ['foo'],
            ['example.com'],
            ['some-path'],
            ['foo:bar'],
            ['C:/Program%20Files/Zend'],
        ];
    }

    public function invalidPathProvider()
    {
        return [
            ['?', '%3F'],
            ['/#', '/%23'],

            // See http://framework.zend.com/issues/browse/ZF-11286
            ['Giri%C5%9F Sayfas%C4%B1.aspx', 'Giri%C5%9F%20Sayfas%C4%B1.aspx']
        ];
    }

    /**
     * Return all methods that are expected to return the same object they
     * are called on, to test that the fluent interface is not broken
     *
     * @return array
     */
    public function fluentInterfaceMethodProvider()
    {
        return [
            ['setScheme',    ['file']],
            ['setUserInfo',  ['userInfo']],
            ['setHost',      ['example.com']],
            ['setPort',      [80]],
            ['setPath',      ['/baz/baz']],
            ['setQuery',     ['foo=bar']],
            ['setFragment',  ['part2']],
            ['makeRelative', ['http://foo.bar/']],
            ['resolve',      ['http://foo.bar/']],
            ['normalize',    []]
        ];
    }

    /**
     * Test cases for absolute URI resolving
     *
     * These examples are taken from RFC-3986 section about relative URI
     * resolving (@link http://tools.ietf.org/html/rfc3986#section-5.4).
     *
     * @return array
     */
    public function resolvedAbsoluteUriProvider()
    {
        return [
            // Normal examples
            ['http://a/b/c/d;p?q', 'g:h',     'g:h'],
            ['http://a/b/c/d;p?q', 'g',       'http://a/b/c/g'],
            ['http://a/b/c/d;p?q', './g',     'http://a/b/c/g'],
            ['http://a/b/c/d;p?q', 'g/',      'http://a/b/c/g/'],
            ['http://a/b/c/d;p?q', '/g',      'http://a/g'],
            ['http://a/b/c/d;p?q', '//g',     'http://g'],
            ['http://a/b/c/d;p?q', '?y',      'http://a/b/c/d;p?y'],
            ['http://a/b/c/d;p?q', 'g?y',     'http://a/b/c/g?y'],
            ['http://a/b/c/d;p?q', '#s',      'http://a/b/c/d;p?q#s'],
            ['http://a/b/c/d;p?q', 'g#s',     'http://a/b/c/g#s'],
            ['http://a/b/c/d;p?q', 'g?y#s',   'http://a/b/c/g?y#s'],
            ['http://a/b/c/d;p?q', ';x',      'http://a/b/c/;x'],
            ['http://a/b/c/d;p?q', 'g;x',     'http://a/b/c/g;x'],
            ['http://a/b/c/d;p?q', 'g;x?y#s', 'http://a/b/c/g;x?y#s'],
            ['http://a/b/c/d;p?q', '',        'http://a/b/c/d;p?q'],
            ['http://a/b/c/d;p?q', '.',       'http://a/b/c/'],
            ['http://a/b/c/d;p?q', './',      'http://a/b/c/'],
            ['http://a/b/c/d;p?q', '..',      'http://a/b/'],
            ['http://a/b/c/d;p?q', '../',     'http://a/b/'],
            ['http://a/b/c/d;p?q', '../g',    'http://a/b/g'],
            ['http://a/b/c/d;p?q', '../..',   'http://a/'],
            ['http://a/b/c/d;p?q', '../../',  'http://a/'],
            ['http://a/b/c/d;p?q', '../../g', 'http://a/g'],

            // Abnormal examples
            ['http://a/b/c/d;p?q', '../../../g',    'http://a/g'],
            ['http://a/b/c/d;p?q', '../../../../g', 'http://a/g'],
            ['http://a/b/c/d;p?q', '/./g',          'http://a/g'],
            ['http://a/b/c/d;p?q', '/../g',         'http://a/g'],
            ['http://a/b/c/d;p?q', 'g.',            'http://a/b/c/g.'],
            ['http://a/b/c/d;p?q', '.g',            'http://a/b/c/.g'],
            ['http://a/b/c/d;p?q', 'g..',           'http://a/b/c/g..'],
            ['http://a/b/c/d;p?q', '..g',           'http://a/b/c/..g'],
            ['http://a/b/c/d;p?q', './../g',        'http://a/b/g'],
            ['http://a/b/c/d;p?q', './g/.',         'http://a/b/c/g/'],
            ['http://a/b/c/d;p?q', 'g/./h',         'http://a/b/c/g/h'],
            ['http://a/b/c/d;p?q', 'g/../h',        'http://a/b/c/h'],
            ['http://a/b/c/d;p?q', 'g;x=1/./y',     'http://a/b/c/g;x=1/y'],
            ['http://a/b/c/d;p?q', 'g;x=1/../y',    'http://a/b/c/y'],
            ['http://a/b/c/d;p?q', 'http:g',        'http:g'],
        ];
    }

    /**
     * Data provider for arrays of query string parameters, with the expected
     * query string
     *
     * @return array
     */
    public function queryParamsArrayProvider()
    {
        return [
            [[
                'foo' => 'bar',
                'baz' => 'waka'
            ], 'foo=bar&baz=waka'],
            [[
                'some key' => 'some crazy value?!#[]&=%+',
                '1'        => ''
            ], 'some%20key=some%20crazy%20value%3F%21%23%5B%5D%26%3D%25%2B&1='],
            [[
                'array'        => ['foo', 'bar', 'baz'],
                'otherstuff[]' => 1234
            ], 'array%5B0%5D=foo&array%5B1%5D=bar&array%5B2%5D=baz&otherstuff%5B%5D=1234']
        ];
    }

    /**
     * Provider for testing removal of extra dot segments in paths
     *
     * @return array
     */
    public function pathWithDotSegmentProvider()
    {
        return [
            ['/a/b/c/./../../g',   '/a/g'],
            ['mid/content=5/../6', 'mid/6']
        ];
    }

    public function normalizedUrlsProvider()
    {
        return [
            ['hTtp://example.com', 'http://example.com/'],
            ['https://EXAMPLE.COM/FOO/BAR', 'https://example.com/FOO/BAR'],
            ['FOO:/bar/with space?que%3fry#frag%ment#', 'foo:/bar/with%20space?que?ry#frag%25ment%23'],
            ['/path/%68%65%6c%6c%6f/world', '/path/hello/world'],
            ['/foo/bar?url=http%3A%2F%2Fwww.example.com%2Fbaz', '/foo/bar?url=http://www.example.com/baz'],

            ['/urlencoded/params?chars=' . urlencode('+&=;%20#'), '/urlencoded/params?chars=%2B%26%3D%3B%2520%23'],
            ['File:///SitePages/fi%6ce%20has%20spaces', 'file:///SitePages/file%20has%20spaces'],
            ['/foo/bar/../baz?do=action#showFragment', '/foo/baz?do=action#showFragment'],

            //  RFC 3986 Capitalizing letters in escape sequences.
            ['http://www.example.com/a%c2%b1b', 'http://www.example.com/a%C2%B1b'],

            // This should be left unchanged, at least for the generic Uri class
            ['http://example.com:80/file?query=bar', 'http://example.com:80/file?query=bar'],
        ];
    }

    public function commonBaseUriProvider()
    {
        return [
             ['http://example.com/dir/subdir/', 'http://example.com/dir/subdir/more/file1.txt', 'more/file1.txt'],
             ['http://example.com/dir/subdir/', 'http://example.com/dir/otherdir/file2.txt',    '../otherdir/file2.txt'],
             ['http://example.com/dir/subdir/', 'http://otherhost.com/dir/subdir/file3.txt',    'http://otherhost.com/dir/subdir/file3.txt'],
        ];
    }


    /**
     * Provider for testing the constructor's behavior on invalid input
     *
     * @return array
     */
    public function invalidConstructorInputProvider()
    {
        return [
            [new \stdClass()],
            [false],
            [true],
            [['scheme' => 'http']],
            [12]
        ];
    }

    /**
     * Provider for testing the behaviors of functions that expect only strings
     *
     * Most of these methods are expected to throw an exception for the
     * provided values
     *
     * @return array
     */
    public function notStringInputProvider()
    {
        return [
            [new Uri('http://foo.bar')],
            [null],
            [12],
            [['scheme' => 'http', 'host' => 'example.com']]
        ];
    }

    public function testParseTwice()
    {
        $uri  = new Uri();
        $uri->parse('http://user@example.com:1/absolute/url?query#fragment');
        $uri->parse('/relative/url');
        $this->assertNull($uri->getScheme());
        $this->assertNull($uri->getHost());
        $this->assertNull($uri->getUserInfo());
        $this->assertNull($uri->getPort());
        $this->assertNull($uri->getQuery());
        $this->assertNull($uri->getFragment());
    }
}
