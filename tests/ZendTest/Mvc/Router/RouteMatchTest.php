<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Router;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Router\RouteMatch;

class RouteMatchTest extends TestCase
{
    public function testParamsAreStored()
    {
        $match = new RouteMatch(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $match->getParams());
    }

    public function testMatchedRouteNameIsSet()
    {
        $match = new RouteMatch([]);
        $match->setMatchedRouteName('foo');

        $this->assertEquals('foo', $match->getMatchedRouteName());
    }

    public function testSetParam()
    {
        $match = new RouteMatch([]);
        $match->setParam('foo', 'bar');

        $this->assertEquals(['foo' => 'bar'], $match->getParams());
    }

    public function testGetParam()
    {
        $match = new RouteMatch(['foo' => 'bar']);

        $this->assertEquals('bar', $match->getParam('foo'));
    }

    public function testGetNonExistentParamWithoutDefault()
    {
        $match = new RouteMatch([]);

        $this->assertNull($match->getParam('foo'));
    }

    public function testGetNonExistentParamWithDefault()
    {
        $match = new RouteMatch([]);

        $this->assertEquals('bar', $match->getParam('foo', 'bar'));
    }
}
