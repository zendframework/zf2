<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper\Url;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack as Router;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Url
     */
    protected $helper;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
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

        $this->helper = new Url;
        $this->helper->setRouter($router);
    }

    public function testHelperHasHardDependencyWithRouter()
    {
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'No RouteStackInterface instance provided');
        $url = new Url();
        $url('home');
    }

    public function testHomeRoute()
    {
        $url = $this->helper->__invoke('home');
        $this->assertEquals('/', $url);
    }

    public function testModuleRoute()
    {
        $url = $this->helper->__invoke('default', array('controller' => 'ctrl', 'action' => 'act'));
        $this->assertEquals('/ctrl/act', $url);
    }

    public function testPluginWithoutRouteMatchesInEventRaisesExceptionWhenNoRouteProvided()
    {

        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'RouteMatch');
        $this->helper->__invoke();
    }

    public function testPluginWithRouteMatchesReturningNoMatchedRouteNameRaisesExceptionWhenNoRouteProvided()
    {
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'matched');

        $this->helper->setRouteMatch(new RouteMatch(array()));
        $this->helper->__invoke();
    }

    public function testPassingNoArgumentsWithValidRouteMatchGeneratesUrl()
    {
        $routeMatch = new RouteMatch(array());
        $routeMatch->setMatchedRouteName('home');
        $this->helper->setRouteMatch($routeMatch);
        $url = $this->helper->__invoke();
        $this->assertEquals('/', $url);
    }

    public function testCanReuseMatchedParameters()
    {
        $this->router->addRoute('replace', array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/:controller/:action',
                'defaults' => array(
                    'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
                ),
            ),
        ));
        $routeMatch = new RouteMatch(array(
            'controller' => 'foo',
        ));
        $routeMatch->setMatchedRouteName('replace');
        $this->helper->setRouteMatch($routeMatch);
        $url = $this->helper->__invoke('replace', array('action' => 'bar'), array(), true);
        $this->assertEquals('/foo/bar', $url);
    }

    public function testCanPassBooleanValueForThirdArgumentToAllowReusingRouteMatches()
    {
        $this->router->addRoute('replace', array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/:controller/:action',
                'defaults' => array(
                    'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
                ),
            ),
        ));
        $routeMatch = new RouteMatch(array(
            'controller' => 'foo',
        ));
        $routeMatch->setMatchedRouteName('replace');
        $this->helper->setRouteMatch($routeMatch);
        $url = $this->helper->__invoke('replace', array('action' => 'bar'), true);
        $this->assertEquals('/foo/bar', $url);
    }

    public function testRemovesModuleRouteListenerParamsWhenReusingMatchedParameters()
    {
        $router = new \Zend\Mvc\Router\Http\TreeRouteStack;
        $router->addRoute('default', array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/:controller/:action',
                'defaults' => array(
                    ModuleRouteListener::MODULE_NAMESPACE => 'ZendTest\Mvc\Controller\TestAsset',
                    'controller' => 'SampleController',
                    'action'     => 'Dash'
                )
            ),
            'child_routes' => array(
                'wildcard' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Wildcard',
                    'options' => array(
                        'param_delimiter'     => '=',
                        'key_value_delimiter' => '%'
                    )
                )
            )
        ));

        $routeMatch = new RouteMatch(array(
            ModuleRouteListener::MODULE_NAMESPACE => 'ZendTest\Mvc\Controller\TestAsset',
            'controller' => 'Rainbow'
        ));
        $routeMatch->setMatchedRouteName('default/wildcard');

        $event = new MvcEvent();
        $event->setRouter($router)
              ->setRouteMatch($routeMatch);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->onRoute($event);

        $helper = new Url();
        $helper->setRouter($router);
        $helper->setRouteMatch($routeMatch);

        $url = $helper->__invoke('default/wildcard', array('Twenty' => 'Cooler'), true);
        $this->assertEquals('/Rainbow/Dash=Twenty%Cooler', $url);
    }
}
