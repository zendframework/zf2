<?php
namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as Request,
    Zend\Mvc\Router\Http\TreeRouteStack,
    Zend\Config\Config;

class TreeRouteStackTest extends TestCase
{
    public function getRouteSpecArray() 
    {
        return array(
            'route1' => array(
                'type' => 'literal',
                'options' => array (
                    'route' =>'/route1',
                    'defaults' => array(
                        'controller' => 'controller1',
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'route1_1' => array(
                        'type' => 'literal',
                        'options' => array (
                            'route' =>'_1',
                            'defaults' => array(
                                'controller' => 'controller1_1'
                            )
                        ),
                    ),
                    'route1_2' => array(
                        'type' => 'literal',
                        'options' => array (
                            'route' =>'_2',
                            'defaults' => array(
                                'controller' => 'controller1_2'
                            )
                        ),
                    )
                )
            )
        );
    }
    
    public function testRouteFromArray()
    {
        $router = new TreeRouteStack();
        $router->addRoutes($this->getRouteSpecArray());
        
        $request = new Request();
        $request->setUri('http://test.net/route1_2');
        
        $match = $router->match($request);
        $this->assertEquals($match->getParam('controller'), 'controller1_2');
    }
    
    
    public function testRouteFromConfig()
    {
        $router = new TreeRouteStack();
        
        $config = new Config($this->getRouteSpecArray());
        
        $router->addRoutes($config);
        
        $request = new Request();
        $request->setUri('http://test.net/route1_2');
        
        $match = $router->match($request);
        $this->assertEquals($match->getParam('controller'), 'controller1_2');
    }
}