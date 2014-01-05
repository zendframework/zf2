<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Controller\Manager\Listener as ControllerManager;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

trait EventTrait
{
    /**
     *
     */
    use Event;

    /**
     * @var ControllerManager
     */
    protected $cm;

    /**
     * @var EventManager
     */
    protected $em;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * @return bool|ControllerManager
     */
    public function controllerManager()
    {
        return $this->cm;
    }

    /**
     * @param ControllerManager $cm
     * @return self
     */
    public function setControllerManager(ControllerManager $cm)
    {
        $this->cm = $cm;
        return $this;
    }

    /**
     * @return EventManager
     */
    public function eventManager()
    {
        return $this->em;
    }

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * @return bool|Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return bool|object
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return bool|Router
     */
    public function router()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     * @return self
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return bool|RouteMatch
     */
    public function routeMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * @return bool|ViewModel
     */
    public function viewModel()
    {
        return $this->viewModel;
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }
}
