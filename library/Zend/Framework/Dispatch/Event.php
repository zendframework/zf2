<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Event as EventManagerEvent;
use Zend\Framework\EventManager\ListenerInterface as EventListener;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ViewModel;

class Event extends EventManagerEvent
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_DISPATCH;

    protected $em;

    protected $controllerLoader;

    protected $request;

    protected $response;

    protected $result;

    protected $routeMatch;

    protected $viewModel;

    public function setEventManager($em)
    {
        $this->em = $em;
        return $this;
    }

    public function getEventManager()
    {
        return $this->em;
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

    public function __invoke(EventListener $listener)
    {
        $response = $listener($this);

        if ($response) {
            $this->setResponse($response);
        }

        $this->eventResponses[] = $response;

        if ($this->callback) {
            call_user_func($this->callback, $this, $listener, $response);
        }
    }
}
