<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Event as EventManagerEvent;
use Zend\Framework\EventManager\ListenerInterface as EventListener;
use Zend\Framework\Controller\AbstractActionController as ActionController;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ViewModel;

class DispatchEvent extends EventManagerEvent
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_CONTROLLER_DISPATCH;

    protected $em;

    protected $error;

    protected $controller;

    protected $controllerLoader;

    protected $request;

    protected $response;

    protected $result;

    protected $routeMatch;

    protected $viewModel;

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setEventManager($em)
    {
        $this->em = $em;
        return $this;
    }

    public function getEventManager()
    {
        return $this->em;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getControllerClass()
    {
        return get_class($this->controller);
    }

    public function setControllerLoader($controllerLoader)
    {
        $this->controllerLoader = $controllerLoader;
        return $this;
    }

    public function getControllerLoader()
    {
        return $this->controllerLoader;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * Invokes listener with this event passed as its only argument.
     *
     * @param $listener
     * @return bool
     */
    public function __invoke(EventListener $listener)
    {
        $response = $listener($this);

        if ($listener instanceof ActionController) {
            $this->setResult($response);
        }

        $this->eventResponses[] = $response;

        if ($this->callback) {
            call_user_func($this->callback, $this, $listener, $response);
        }
    }
}
