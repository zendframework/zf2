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
use ArrayIterator;
use Zend\Http\Request as Request;
use Zend\Http\PhpEnvironment\Request as PhpRequest;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Uri\Http as HttpUri;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\Http\Hostname;
use ZendTest\Mvc\Router\FactoryTester;

class TreeRouteStackTest extends TestCase
{
    public function testAddRouteRequiresHttpSpecificRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Route definition must be an array or Traversable object');
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new \ZendTest\Mvc\Router\TestAsset\DummyRoute());
    }

    public function testAddRouteViaStringRequiresHttpSpecificRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Given route does not implement HTTP route interface');
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', [
            'type' => '\ZendTest\Mvc\Router\TestAsset\DummyRoute'
        ]);
    }

    public function testAddRouteAcceptsTraversable()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new ArrayIterator([
            'type' => '\ZendTest\Mvc\Router\Http\TestAsset\DummyRoute'
        ]));
    }

    public function testNoMatchWithoutUriMethod()
    {
        $stack  = new TreeRouteStack();
        $request = new BaseRequest();

        $this->assertNull($stack->match($request));
    }

    public function testSetBaseUrlFromFirstMatch()
    {
        $stack = new TreeRouteStack();

        $request = new PhpRequest();
        $request->setBaseUrl('/foo');
        $stack->match($request);
        $this->assertEquals('/foo', $stack->getBaseUrl());

        $request = new PhpRequest();
        $request->setBaseUrl('/bar');
        $stack->match($request);
        $this->assertEquals('/foo', $stack->getBaseUrl());
    }

    public function testBaseUrlLengthIsPassedAsOffset()
    {
        $stack = new TreeRouteStack();
        $stack->setBaseUrl('/foo');
        $stack->addRoute('foo', [
            'type' => '\ZendTest\Mvc\Router\Http\TestAsset\DummyRoute'
        ]);

        $this->assertEquals(4, $stack->match(new Request())->getParam('offset'));
    }

    public function testNoOffsetIsPassedWithoutBaseUrl()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', [
            'type' => '\ZendTest\Mvc\Router\Http\TestAsset\DummyRoute'
        ]);

        $this->assertEquals(null, $stack->match(new Request())->getParam('offset'));
    }

    public function testAssemble()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('', $stack->assemble([], ['name' => 'foo']));
    }

    public function testAssembleCanonicalUriWithoutRequestUri()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Request URI has not been set');
        $stack = new TreeRouteStack();

        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('http://example.com:8080/', $stack->assemble([], ['name' => 'foo', 'force_canonical' => true]));
    }

    public function testAssembleCanonicalUriWithRequestUri()
    {
        $uri   = new HttpUri('http://example.com:8080/');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);

        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('http://example.com:8080/', $stack->assemble([], ['name' => 'foo', 'force_canonical' => true]));
    }

    public function testAssembleCanonicalUriWithGivenUri()
    {
        $uri   = new HttpUri('http://example.com:8080/');
        $stack = new TreeRouteStack();

        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $this->assertEquals('http://example.com:8080/', $stack->assemble([], ['name' => 'foo', 'uri' => $uri, 'force_canonical' => true]));
    }

    public function testAssembleCanonicalUriWithHostnameRoute()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new Hostname('example.com'));
        $uri   = new HttpUri();
        $uri->setScheme('http');

        $this->assertEquals('http://example.com/', $stack->assemble([], ['name' => 'foo', 'uri' => $uri]));
    }

    public function testAssembleCanonicalUriWithHostnameRouteWithoutScheme()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Request URI has not been set');
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new Hostname('example.com'));
        $uri   = new HttpUri();

        $this->assertEquals('http://example.com/', $stack->assemble([], ['name' => 'foo', 'uri' => $uri]));
    }

    public function testAssembleCanonicalUriWithHostnameRouteAndRequestUriWithoutScheme()
    {
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute('foo', new Hostname('example.com'));

        $this->assertEquals('http://example.com/', $stack->assemble([], ['name' => 'foo']));
    }

    public function testAssembleCanonicalUriWithHostnameRouteAndQueryRoute()
    {
        $this->markTestSkipped('Query route part has been deprecated in ZF as of 2.1.4');
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute(
            'foo',
            [
                'type' => 'Hostname',
                'options' => [
                    'route' => 'example.com',
                ],
                'child_routes' => [
                    'index' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/',
                        ],
                        'child_routes' => [
                            'query' => [
                                'type' => 'Query',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->assertEquals('http://example.com/?bar=baz', $stack->assemble(['bar' => 'baz'], ['name' => 'foo/index/query']));
    }

    public function testAssembleWithQueryRoute()
    {
        $this->markTestSkipped('Query route part has been deprecated in ZF as of 2.1.4');
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute(
            'index',
            [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                ],
                'child_routes' => [
                    'query' => [
                        'type' => 'Query',
                    ],
                ],
            ]
        );

        $this->assertEquals('/?bar=baz', $stack->assemble(['bar' => 'baz'], ['name' => 'index/query']));
    }

    public function testAssembleWithQueryParams()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                ],
            ]
        );

        $this->assertEquals('/?foo=bar', $stack->assemble([], ['name' => 'index', 'query' => ['foo' => 'bar']]));
    }

    public function testAssembleWithEncodedPath()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            [
                'type' => 'Literal',
                'options' => [
                    'route' => '/this%2Fthat',
                ],
            ]
        );

        $this->assertEquals('/this%2Fthat', $stack->assemble([], ['name' => 'index']));
    }

    public function testAssembleWithEncodedPathAndQueryParams()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            [
                'type' => 'Literal',
                'options' => [
                    'route' => '/this%2Fthat',
                ],
            ]
        );

        $this->assertEquals('/this%2Fthat?foo=bar', $stack->assemble([], ['name' => 'index', 'query' => ['foo' => 'bar'], 'normalize_path' => false]));
    }

    public function testAssembleWithScheme()
    {
        $uri   = new HttpUri();
        $uri->setScheme('http');
        $uri->setHost('example.com');
        $stack = new TreeRouteStack();
        $stack->setRequestUri($uri);
        $stack->addRoute(
            'secure',
            [
                'type' => 'Scheme',
                'options' => [
                    'scheme' => 'https'
                ],
                'child_routes' => [
                    'index' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/',
                        ],
                    ],
                ],
            ]
        );
        $this->assertEquals('https://example.com/', $stack->assemble([], ['name' => 'secure/index']));
    }

    public function testAssembleWithFragment()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                ],
            ]
        );

        $this->assertEquals('/#foobar', $stack->assemble([], ['name' => 'index', 'fragment' => 'foobar']));
    }

    public function testAssembleWithoutNameOption()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Missing "name" option');
        $stack = new TreeRouteStack();
        $stack->assemble();
    }

    public function testAssembleNonExistentRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Route with name "foo" not found');
        $stack = new TreeRouteStack();
        $stack->assemble([], ['name' => 'foo']);
    }

    public function testAssembleNonExistentChildRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Route with name "index" does not have child routes');
        $stack = new TreeRouteStack();
        $stack->addRoute(
            'index',
            [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                ],
            ]
        );
        $stack->assemble([], ['name' => 'index/foo']);
    }

    public function testDefaultParamIsAddedToMatch()
    {
        $stack = new TreeRouteStack();
        $stack->setBaseUrl('/foo');
        $stack->addRoute('foo', new TestAsset\DummyRoute());
        $stack->setDefaultParam('foo', 'bar');

        $this->assertEquals('bar', $stack->match(new Request())->getParam('foo'));
    }

    public function testDefaultParamDoesNotOverrideParam()
    {
        $stack = new TreeRouteStack();
        $stack->setBaseUrl('/foo');
        $stack->addRoute('foo', new TestAsset\DummyRouteWithParam());
        $stack->setDefaultParam('foo', 'baz');

        $this->assertEquals('bar', $stack->match(new Request())->getParam('foo'));
    }

    public function testDefaultParamIsUsedForAssembling()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new TestAsset\DummyRouteWithParam());
        $stack->setDefaultParam('foo', 'bar');

        $this->assertEquals('bar', $stack->assemble([], ['name' => 'foo']));
    }

    public function testDefaultParamDoesNotOverrideParamForAssembling()
    {
        $stack = new TreeRouteStack();
        $stack->addRoute('foo', new TestAsset\DummyRouteWithParam());
        $stack->setDefaultParam('foo', 'baz');

        $this->assertEquals('bar', $stack->assemble(['foo' => 'bar'], ['name' => 'foo']));
    }

    public function testSetBaseUrl()
    {
        $stack = new TreeRouteStack();

        $this->assertEquals($stack, $stack->setBaseUrl('/foo/'));
        $this->assertEquals('/foo', $stack->getBaseUrl());
    }

    public function testSetRequestUri()
    {
        $uri   = new HttpUri();
        $stack = new TreeRouteStack();

        $this->assertEquals($stack, $stack->setRequestUri($uri));
        $this->assertEquals($uri, $stack->getRequestUri());
    }

    public function testPriorityIsPassedToPartRoute()
    {
        $stack = new TreeRouteStack();
        $stack->addRoutes([
            'foo' => [
                'type' => 'Literal',
                'priority' => 1000,
                'options' => [
                    'route' => '/foo',
                    'defaults' => [
                        'controller' => 'foo',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'bar' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/bar',
                            'defaults' => [
                                'controller' => 'foo',
                                'action'     => 'bar',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $reflectedClass    = new \ReflectionClass($stack);
        $reflectedProperty = $reflectedClass->getProperty('routes');
        $reflectedProperty->setAccessible(true);
        $routes = $reflectedProperty->getValue($stack);

        $this->assertEquals(1000, $routes->get('foo')->priority);
    }

    public function testPrototypeRoute()
    {
        $stack = new TreeRouteStack();
        $stack->addPrototype(
            'bar',
            ['type' => 'literal', 'options' => ['route' => '/bar']]
        );
        $stack->addRoute('foo', 'bar');
        $this->assertEquals('/bar', $stack->assemble([], ['name' => 'foo']));
    }

    public function testChainRouteAssembling()
    {
        $stack = new TreeRouteStack();
        $stack->addPrototype(
            'bar',
            ['type' => 'literal', 'options' => ['route' => '/bar']]
        );
        $stack->addRoute(
            'foo',
            [
                'type' => 'literal',
                'options' => [
                    'route' => '/foo'
                ],
                'chain_routes' => [
                    'bar'
                ],
            ]
        );
        $this->assertEquals('/foo/bar', $stack->assemble([], ['name' => 'foo']));
    }

    public function testChainRouteAssemblingWithChildrenAndSecureScheme()
    {
        $stack = new TreeRouteStack();

        $uri = new \Zend\Uri\Http();
        $uri->setHost('localhost');

        $stack->setRequestUri($uri);
        $stack->addRoute(
            'foo',
            [
                'type' => 'literal',
                'options' => [
                    'route' => '/foo'
                ],
                'chain_routes' => [
                    ['type' => 'scheme', 'options' => ['scheme' => 'https']]
                ],
                'child_routes' => [
                    'baz' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/baz'
                        ],
                    ]
                ]
            ]
        );
        $this->assertEquals('https://localhost/foo/baz', $stack->assemble([], ['name' => 'foo/baz']));
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\TreeRouteStack',
            [],
            []
        );
    }
}
