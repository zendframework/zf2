<?php
namespace ZendTest\Mvc\Router\Http;

use Zend\Mvc\Router\Http\HostnameOptionalSubdomain;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Stdlib\Request as BaseRequest,
    Zend\Uri\Http as HttpUri,
    Zend\Mvc\Router\Http\Hostname,
    ZendTest\Mvc\Router\FactoryTester;

class HostnameOptionalSubdomainTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new HostnameOptionalSubdomain('foo.example.com'),
                'foo.example.com',
                array(),
                null
            ),
        	'simple-match-with-param' => array(
                new HostnameOptionalSubdomain(':foo.example.com'),
                'bar.example.com',
                array('foo' => 'bar'),
                null
            ),
            'no-match-on-different-hostname' => array(
                new HostnameOptionalSubdomain('foo.example.com'),
                'bar.example.com',
                null,
                null
            ),
            'no-match-with-different-number-of-non-optional-parts' => array(
                new HostnameOptionalSubdomain('foo.example.com'),
                'example.com',
                null,
                null
            ),
            'no-match-with-too-many-parts' => array(
                new HostnameOptionalSubdomain(':foo.example.com'),
                'bar.baz.example.com',
                null,
                null
            ),
            'match-overrides-default' => array(
                new HostnameOptionalSubdomain(':foo.example.com', array(), array('foo' => 'baz')),
                'bat.example.com',
                array('foo' => 'bat'),
                null
            ),
            'constraints-prevent-match' => array(
                new HostnameOptionalSubdomain(':foo.example.com', array('foo' => '\d+')),
                'bar.example.com',
                null,
                null
            ),
            'constraints-allow-match' => array(
                new HostnameOptionalSubdomain(':foo.example.com', array('foo' => '\d+')),
                '123.example.com',
                array('foo' => '123'),
                null
            ),
            'match-optional-subdomain-parts' => array(
                new HostnameOptionalSubdomain(':bar.:foo.example.com'),
                'bat.example.com',
                array('foo' => 'bat', 'bar' => null),
                null
            ),
            'match-optional-subdomain-parts-with-defaults' => array(
                new HostnameOptionalSubdomain(':bar.:foo.example.com', array(), array('bar' => 'baz')),
                'bat.example.com',
                array('foo' => 'bat', 'bar' => 'baz'),
                'baz.bat.example.com',
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Hostname $route
     * @param        string   $hostname
     * @param        array    $params
     */
    public function testMatching(HostnameOptionalSubdomain $route, $hostname, array $params = null, $onlyUsedForAssembly = null)
    {
        $request = new Request();
        $request->setUri('http://' . $hostname . '/');
        $match = $route->match($request);

        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);

            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Hostname $route
     * @param        string   $hostname
     * @param        array    $params
     */
    public function testAssembling(HostnameOptionalSubdomain $route, $hostname, array $params = null, $assembledHostname = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $uri  = new HttpUri();
        $path = $route->assemble($params, array('uri' => $uri));

        $this->assertEquals('', $path);
        if (isset($assembledHostname)) {
            $this->assertEquals($assembledHostname, $uri->getHost());
        } else {
            $this->assertEquals($hostname, $uri->getHost());
        }
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = new HostnameOptionalSubdomain('example.com');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testGetAssembledParams()
    {
        $route = new HostnameOptionalSubdomain(':foo.example.com');
        $uri   = new HttpUri();
        $route->assemble(array('foo' => 'bar', 'baz' => 'bat'), array('uri' => $uri));

        $this->assertEquals(array('foo'), $route->getAssembledParams());
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\HostnameOptionalSubdomain',
            array(
                'route' => 'Missing "route" in options array'
            ),
            array(
                'route' => 'example.com'
            )
        );
    }
}

