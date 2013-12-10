<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ApplicationInterface as ApplicationInterface;
use Zend\Framework\EventManager\Event;
use Zend\Framework\EventManager\ListenerInterface as EventListener;
use Zend\Framework\ServiceManager\Config as ServiceManagerConfig;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Mvc\Router\RouteInterface;

use Zend\View\Model\ViewModel;

class MvcEvent extends Event implements FactoryInterface
{
    /**
     *
     */
    const EVENT_BOOTSTRAP           = 'mvc.bootstrap';
    const EVENT_DISPATCH            = 'mvc.dispatch';
    const EVENT_CONTROLLER_DISPATCH = 'mvc.controller.dispatch';
    const EVENT_DISPATCH_ERROR      = 'mvc.dispatch.error';
    const EVENT_RESPONSE            = 'mvc.response';
    const EVENT_RENDER              = 'mvc.render';
    const EVENT_RENDER_ERROR        = 'mvc.render.error';
    const EVENT_ROUTE               = 'mvc.route';

    /**
     * @var string
     */
    protected $name = 'mvc.application';

    /**
     * @var
     */
    protected $application;

    protected $em;

    protected $controllerLoader;

    protected $request;

    protected $response;

    protected $result;

    protected $remote;

    protected $routeMatch;

    protected $sm;

    protected $viewModel;

    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
        return $this;
    }

    public function getApplication()
    {
        return $this->application;
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

    public function setRouter(RouteInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
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

    public function setServiceManager(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    public function getServiceManager()
    {
        return $this->sm;
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
        return parent::__invoke($listener);
    }

    public function createService(ServiceManagerInterface $sm)
    {
        $event = new MvcEvent;

        $app = $sm->get(new ServiceRequest('Application'));
        $em = $app->getEventManager();

        $config = $app->getConfig();

        foreach($config['default_listeners'] as $l) {
            $em->attach($sm->get(new ServiceRequest($l)));
        }

        $event->setApplication($app)
              ->setEventManager($app->getEventManager())
              ->setServiceManager($app->getServiceManager())
              ->setRequest($app->getRequest())
              ->setResponse($app->getResponse())
              ->setRouter($app->getRouter())
              ->setControllerLoader($app->getControllerLoader())
              ->setViewModel($app->getViewModel());

        return $event;
    }
}
