<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\EventManager,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    Zend\Mvc\View\InjectRouteMatchListener,
    Zend\View\Model\ViewModel,
    Zend\Stdlib\Request,
    Zend\Http\Request as HttpRequest;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InjectRouteMatchListenerTest extends TestCase
{
    /**
     * @var InjectRouteMatchlistener
     */
    protected $listener;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var Request
     */
    protected $request;

    public function setUp()
    {
        $this->listener   = new InjectRouteMatchListener();
        $this->event      = new MvcEvent();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array());
    }

    public function testBasicInjection()
    {
        $this->event->setRouteMatch($this->routeMatch);
        $this->event->setRequest($this->request);

        $this->listener->injectRouteMatch($this->event);
        $this->assertSame($this->routeMatch, $this->request->routeMatch());
    }

    public function testLackOfRouteMatchBypasesInjection()
    {
        $this->event->setRequest($this->request);

        $this->assertNull($this->listener->injectRouteMatch($this->event));
        $this->assertInstanceOf('\Zend\Stdlib\Parameters', $this->request->routeMatch());
        $this->assertNotSame($this->routeMatch, $this->request->routeMatch());

    }

    public function testLackOfRequestObjectBypasesInjection()
    {
        $this->event->setRouteMatch($this->routeMatch);

        $this->assertNull($this->listener->injectRouteMatch($this->event));
        $this->assertInstanceOf('\Zend\Stdlib\Parameters', $this->request->routeMatch());
        $this->assertNotSame($this->routeMatch, $this->request->routeMatch());

    }
}
