<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request as Request;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Uri\Http as HttpUri;
use Zend\Mvc\Router\Http\Hostname;
use ZendTest\Mvc\Router\FactoryTester;

class HostnameTest extends TestCase
{
    public static function routeProvider()
    {
        return array(
            'simple-match' => array(
                new Hostname(':foo.example.com'),
                'bar.example.com',
                array('foo' => 'bar')
            ),
            'no-match-on-different-hostname' => array(
                new Hostname('foo.example.com'),
                'bar.example.com',
                null
            ),
            'no-match-with-different-number-of-parts' => array(
                new Hostname('foo.example.com'),
                'example.com',
                null
            ),
            'no-match-with-different-number-of-parts-2' => array(
                new Hostname('example.com'),
                'foo.example.com',
                null
            ),
            'match-overrides-default' => array(
                new Hostname(':foo.example.com', array(), array('foo' => 'baz')),
                'bat.example.com',
                array('foo' => 'bat')
            ),
            'constraints-prevent-match' => array(
                new Hostname(':foo.example.com', array('foo' => '\d+')),
                'bar.example.com',
                null
            ),
            'constraints-allow-match' => array(
                new Hostname(':foo.example.com', array('foo' => '\d+')),
                '123.example.com',
                array('foo' => '123')
            ),
            'optional-subdomain' => array(
                new Hostname('[:foo].example.com'),
                'bar.example.com',
                array('foo' => 'bar'),
            ),
            'two-optional-subdomain' => array(
                new Hostname('[:foo].[:bar].example.com'),
                'baz.bat.example.com',
                array('foo' => 'baz', 'bar' => 'bat'),
            ),
            'missing-optional-subdomain' => array(
                new Hostname('[:foo].example.com'),
                'example.com',
                array('foo' => null),
            ),
            'one-of-two-missing-optional-subdomain' => array(
                new Hostname('[:foo].[:bar].example.com'),
                'bat.example.com',
                array('foo' => null, 'bar' => 'bat'),
            ),
            'two-missing-optional-subdomain' => array(
                new Hostname('[:foo].[:bar].example.com'),
                'example.com',
                array('foo' => null, 'bar' => null),
            ),
            'no-match-on-different-hostname-and-optional-subdomain' => array(
                new Hostname('[:foo].example.com'),
                'bar.test.com',
                null,
            ),
            'no-match-with-different-number-of-parts-and-optional-subdomain' => array(
                new Hostname('[:foo].example.com'),
                'bar.baz.example.com',
                null,
            ),
            'match-overrides-default-optional-subdomain' => array(
                new Hostname('[:foo].:bar.example.com', array(), array('bar' => 'baz')),
                'bat.qux.example.com',
                array('foo' => 'bat', 'bar' => 'qux'),
            ),
            'constraints-prevent-match-optional-subdomain' => array(
                new Hostname('[:foo].example.com', array('foo' => '\d+')),
                'bar.example.com',
                null,
            ),
            'constraints-allow-match-optional-subdomain' => array(
                new Hostname('[:foo].example.com', array('foo' => '\d+')),
                '123.example.com',
                array('foo' => '123'),
            ),
            'invalid-optional-subdomain' => array(
                new Hostname(':foo.[:bar].example.com'),
                'baz.bat.example.com',
                null,
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Hostname $route
     * @param        string   $hostname
     * @param        array    $params
     */
    public function testMatching(Hostname $route, $hostname, array $params = null)
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
    public function testAssembling(Hostname $route, $hostname, array $params = null)
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $uri  = new HttpUri();
        $path = $route->assemble($params, array('uri' => $uri));

        $this->assertEquals('', $path);
        $this->assertEquals($hostname, $uri->getHost());
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Hostname('example.com');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testAssemblingWithMissingParameter()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Missing parameter "foo"');

        $route = new Hostname(':foo.example.com');
        $uri   = new HttpUri();
        $route->assemble(array(), array('uri' => $uri));
    }

    public function testAssemblingWithInvalidOptionalSubdomain()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Non-optional segment found before optional');

        $route = new Hostname(':foo.[:bar].example.com');
        $uri   = new HttpUri();
        $route->assemble(array('foo' => 'baz', 'bar' => 'bat'), array('uri' => $uri));
    }

    public function testGetAssembledParams()
    {
        $route = new Hostname(':foo.example.com');
        $uri   = new HttpUri();
        $route->assemble(array('foo' => 'bar', 'baz' => 'bat'), array('uri' => $uri));

        $this->assertEquals(array('foo'), $route->getAssembledParams());
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Hostname',
            array(
                'route' => 'Missing "route" in options array'
            ),
            array(
                'route' => 'example.com'
            )
        );
    }
}
