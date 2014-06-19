<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\Http\Request;
use Zend\Mvc\View\Helper\MatchedRouteName;
use Zend\Mvc\Router\SimpleRouteStack as Router;

/**
 * @group  Zend_View
 * @group  Zend_View_Helper
 * @covers \Zend\Mvc\View\Helper\MatchedRouteName
 */
class MatchedRouteNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MatchedRouteName
     */
    private $matchedRouteHelper;

    protected function setUp()
    {
        $router = new Router();
        $router->addRoute('home', array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/',
            )
        ));
        $router->addRoute('default', array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/:controller[/:action]',
                )
        ));
        $this->router = $router;

        $request = new Request();
        $this->request = $request;

        $this->matchedRouteHelper = new MatchedRouteName();
        $this->matchedRouteHelper->setRouter($router);
        $this->matchedRouteHelper->setRequest($request);
    }

    public function testHelperHasHardDependencyWithRouter()
    {
        $matchedRouteHelper = new MatchedRouteName();
        $matchedRouteHelper->setRequest($this->request);
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'No RouteStackInterface instance provided');
        $matchedRouteHelper->__invoke('home');
        $this->setExpectedException('PHPUnit_Framework_Error');
        $matchedRouteHelper->setRouter(null);
    }

    public function testHelperHasHardDependencyWithRequest()
    {
        $matchedRouteHelper = new MatchedRouteName();
        $matchedRouteHelper->setRouter($this->router);
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'No RequestInterface instance provided');
        $matchedRouteHelper->__invoke('home');
        $this->setExpectedException('PHPUnit_Framework_Error');
        $matchedRouteHelper->setRequest(null);
    }

    public function testHomeRoute()
    {
        $this->request->setUri('/');
        $matchedRoute = $this->matchedRouteHelper->__invoke();
        $this->assertEquals('home', $matchedRoute);
    }

    public function testModuleRoute()
    {
        $this->request->setUri('/foo/bar');
        $matchedRoute = $this->matchedRouteHelper->__invoke();
        $this->assertEquals('default', $matchedRoute);
    }
}
