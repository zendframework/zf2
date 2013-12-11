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
use Zend\Framework\View\ManagerInterface as ViewManager;
use Zend\Framework\ApplicationInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface;
use Zend\Framework\View\Model\ViewModel as ViewModel;


class Event extends EventManagerEvent
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_DISPATCH;

    protected $em;

    protected $sm;

    protected $vm;

    protected $controllerLoader;

    protected $request;

    protected $response;

    protected $result;

    protected $routeMatch;

    protected $application;

    protected $viewModel;

    protected $view;
    protected $pm;
    protected $resolver;
    protected $viewConfig;

    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    public function getViewModel()
    {
        return $this->viewModel;
    }

    public function setViewManager($vm)
    {
        $this->vm = $vm;
        return $this;
    }

    /**
     * @return ViewManager
     */
    public function getViewManager()
    {
        return $this->vm;
    }


    /**
     * Set application instance
     *
     * @param  ApplicationInterface $application
     * @return MvcEvent
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Get application instance
     *
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    public function setServiceManager(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
        return $this;
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

    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    public function getViewResolver()
    {
        return $this->resolver;
    }

    public function setViewResolver($resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }

    public function getViewPluginManager()
    {
        return $this->pm;
    }

    public function setViewPluginManager($pm)
    {
        $this->pm = $pm;
        return $this;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    public function getViewConfig()
    {
        return $this->viewConfig;
    }

    public function setViewConfig($viewConfig)
    {
        $this->viewConfig = $viewConfig;
        return $this;
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
