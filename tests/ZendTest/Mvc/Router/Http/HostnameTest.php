<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
        return [
            'simple-match' => [
                new Hostname(':foo.example.com'),
                'bar.example.com',
                ['foo' => 'bar']
            ],
            'no-match-on-different-hostname' => [
                new Hostname('foo.example.com'),
                'bar.example.com',
                null
            ],
            'no-match-with-different-number-of-parts' => [
                new Hostname('foo.example.com'),
                'example.com',
                null
            ],
            'no-match-with-different-number-of-parts-2' => [
                new Hostname('example.com'),
                'foo.example.com',
                null
            ],
            'match-overrides-default' => [
                new Hostname(':foo.example.com', [], ['foo' => 'baz']),
                'bat.example.com',
                ['foo' => 'bat']
            ],
            'constraints-prevent-match' => [
                new Hostname(':foo.example.com', ['foo' => '\d+']),
                'bar.example.com',
                null
            ],
            'constraints-allow-match' => [
                new Hostname(':foo.example.com', ['foo' => '\d+']),
                '123.example.com',
                ['foo' => '123']
            ],
            'constraints-allow-match-2' => [
                new Hostname(
                    'www.:domain.com',
                    ['domain' => '(mydomain|myaltdomain1|myaltdomain2)'],
                    ['domain'    => 'mydomain']
                ),
                'www.mydomain.com',
                ['domain' => 'mydomain']
            ],
            'optional-subdomain' => [
                new Hostname('[:foo.]example.com'),
                'bar.example.com',
                ['foo' => 'bar'],
            ],
            'two-optional-subdomain' => [
                new Hostname('[:foo.][:bar.]example.com'),
                'baz.bat.example.com',
                ['foo' => 'baz', 'bar' => 'bat'],
            ],
            'missing-optional-subdomain' => [
                new Hostname('[:foo.]example.com'),
                'example.com',
                ['foo' => null],
            ],
            'one-of-two-missing-optional-subdomain' => [
                new Hostname('[:foo.][:bar.]example.com'),
                'bat.example.com',
                ['foo' => null, 'foo' => 'bat'],
            ],
            'two-missing-optional-subdomain' => [
                new Hostname('[:foo.][:bar.]example.com'),
                'example.com',
                ['foo' => null, 'bar' => null],
            ],
            'two-optional-subdomain-nested' => [
                new Hostname('[[:foo.]:bar.]example.com'),
                'baz.bat.example.com',
                ['foo' => 'baz', 'bar' => 'bat'],
            ],
            'one-of-two-missing-optional-subdomain-nested' => [
                new Hostname('[[:foo.]:bar.]example.com'),
                'bat.example.com',
                ['foo' => null, 'bar' => 'bat'],
            ],
            'two-missing-optional-subdomain-nested' => [
                new Hostname('[[:foo.]:bar.]example.com'),
                'example.com',
                ['foo' => null, 'bar' => null],
            ],
            'no-match-on-different-hostname-and-optional-subdomain' => [
                new Hostname('[:foo.]example.com'),
                'bar.test.com',
                null,
            ],
            'no-match-with-different-number-of-parts-and-optional-subdomain' => [
                new Hostname('[:foo.]example.com'),
                'bar.baz.example.com',
                null,
            ],
            'match-overrides-default-optional-subdomain' => [
                new Hostname('[:foo.]:bar.example.com', [], ['bar' => 'baz']),
                'bat.qux.example.com',
                ['foo' => 'bat', 'bar' => 'qux'],
            ],
            'constraints-prevent-match-optional-subdomain' => [
                new Hostname('[:foo.]example.com', ['foo' => '\d+']),
                'bar.example.com',
                null,
            ],
            'constraints-allow-match-optional-subdomain' => [
                new Hostname('[:foo.]example.com', ['foo' => '\d+']),
                '123.example.com',
                ['foo' => '123'],
            ],
            'middle-subdomain-optional' => [
                new Hostname(':foo.[:bar.]example.com'),
                'baz.bat.example.com',
                ['foo' => 'baz', 'bar' => 'bat'],
            ],
            'missing-middle-subdomain-optional' => [
                new Hostname(':foo.[:bar.]example.com'),
                'baz.example.com',
                ['foo' => 'baz'],
            ],
            'non-standard-delimeter' => [
                new Hostname('user-:username.example.com'),
                'user-jdoe.example.com',
                ['username' => 'jdoe'],
            ],
            'non-standard-delimeter-optional' => [
                new Hostname(':page{-}[-:username].example.com'),
                'article-jdoe.example.com',
                ['page' => 'article', 'username' => 'jdoe'],
            ],
            'missing-non-standard-delimeter-optional' => [
                new Hostname(':page{-}[-:username].example.com'),
                'article.example.com',
                ['page' => 'article'],
            ],
        ];
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
        $path = $route->assemble($params, ['uri' => $uri]);

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
        $route->assemble([], ['uri' => $uri]);
    }

    public function testGetAssembledParams()
    {
        $route = new Hostname(':foo.example.com');
        $uri   = new HttpUri();
        $route->assemble(['foo' => 'bar', 'baz' => 'bat'], ['uri' => $uri]);

        $this->assertEquals(['foo'], $route->getAssembledParams());
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Hostname',
            [
                'route' => 'Missing "route" in options array'
            ],
            [
                'route' => 'example.com'
            ]
        );
    }

    /**
     * @group zf5656
     */
    public function testFailedHostnameSegmentMatchDoesNotEmitErrors()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException');
        $route = new Hostname(':subdomain.with_underscore.com');
    }
}
