<?php

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\SharedEventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Controller\PluginBroker,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch;
use Zend\Json\Json;

class ActionControllerTest extends TestCase
{
    public $controller;
    public $serviceMapArray;
    public $event;
    public $request;
    public $response;

    public function setUp()
    {
        $this->controller = new TestAsset\JsonRpcTestController();
        $this->serviceMapArray = Json::decode(
            include __DIR__ . '/TestAsset/JsonRpcTestControllerServiceMap.php', 
            Json::TYPE_ARRAY
        );
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'json-rpc-test'));
        $this->event      = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
    }

    public function testServiceMap()
    {
        $this->request->setMethod(Request::METHOD_GET);
        $result = $this->controller->dispatch($this->request, $this->response);        
        $serviceMapArray = $result->getVariables();
        $this->assertEquals($this->serviceMapArray, $serviceMapArray);
    }
    
    public function testMethodHandle()
    {
        $expectedArray = array(
            "id" => null,
            "result" => 3,
            "error" => null
        );
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "add", "params": [1, 2], "id": null}');
        $result = $this->controller->dispatch($this->request, $this->response);        
        $returnArray = $result->getVariables();
        
        $this->assertEquals($expectedArray, $returnArray);
        
        $expectedArray = array(
            "id" => null,
            "result" => "HelloWorld",
            "error" => null
        );
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "concat", "params": ["Hello", "World"], "id": null}');
        $result = $this->controller->dispatch($this->request, $this->response);        
        $returnArray = $result->getVariables();
        
        $this->assertEquals($expectedArray, $returnArray);         
    }
    
    public function testMethodNotFound()
    {
        $expectedArray = array(
            "id" => null,
            "result" => null,
            "error" => 'Method doesNotExist not found'
        );
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "doesNotExist", "params": [1, 2], "id": null}');
        $result = $this->controller->dispatch($this->request, $this->response);        
        $returnArray = $result->getVariables();
        
        $this->assertEquals($expectedArray, $returnArray);        
    }
}
