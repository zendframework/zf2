<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ApplicationInterface as Application;
use Zend\Framework\EventManager\EventManagerInterface as EventManager;
use Zend\Framework\View\Manager as ViewManager;
use Zend\Framework\View\Model\ViewModel;
//use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\ServiceManager\ServiceRequest;

trait ApplicationServiceTrait
{
    /**
     * @param $name
     * @return bool|object
     */
    public function getService($name)
    {
        return $this->sm->get(new ServiceRequest($name));
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function addService($name, $service)
    {
        $this->sm->add($name, $service);
        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->getService('ServiceManager');
    }

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceManager(ServiceManager $sm)
    {
        return $this->addService('ServiceManager', $sm);
    }

    /**
     * @return ViewManager
     */
    public function getViewManager()
    {
        return $this->getService('ViewManager');
    }

    /**
     * @param ViewManager $vm
     * @return $this
     */
    public function setViewManager(ViewManager $vm)
    {
        return $this->addService('ViewManager', $vm);
    }

    /**
     * @return array
     */
    public function getApplicationConfig()
    {
        return $this->getService('ApplicationConfig');
    }

    /**
     * @param $config
     * @return $this
     */
    public function setApplicationConfig($config)
    {
        return $this->addService('ApplicationConfig', $config);
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->getService('Application');
    }

    /**
     * @param Application $application
     * @return $this
     */
    public function setApplication(Application $application)
    {
        return $this->addService('Application', $application);
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->getService('EventManager');
    }

    /**
     * @param EventManager $em
     * @return $this
     */
    public function setEventManager(EventManager $em)
    {
        return $this->addService('EventManager', $em);
    }

    /**
     * @return ViewConfig
     */
    public function getViewConfig()
    {
        return $this->getViewManager()->getViewConfig();
    }

    /**
     * @return bool|object
     */
    public function getViewResolver()
    {
        return $this->getService('ViewResolver');
    }

    /**
     * @param $resolver
     * @return $this
     */
    public function setViewResolver($resolver)
    {
        return $this->addService('ViewResolver', $resolver);
    }

    /**
     * @return bool|object
     */
    public function getRequest()
    {
        return $this->getService('Request');
    }

    /**
     * @param $request
     * @return $this
     */
    public function setRequest($request)
    {
        return $this->addService('Request', $request);
    }

    /**
     * @return bool|object
     */
    public function getResponse()
    {
        return $this->getService('Response');
    }

    /**
     * @param $response
     * @return $this
     */
    public function setResponse($response)
    {
        return $this->addService('Response', $response);
    }

    /**
     * @return bool|object
     */
    public function getRouter()
    {
        return $this->getService('Router');
    }

    /**
     * @param $router
     * @return $this
     */
    public function setRouter($router)
    {
        return $this->addService('Router', $router);
    }

    /**
     * @return bool|object
     */
    public function getRouteMatch()
    {
        return $this->getService('RouteMatch');
    }

    /**
     * @param $routeMatch
     * @return $this
     */
    public function setRouteMatch($routeMatch)
    {
        return $this->addService('RouteMatch', $routeMatch);
    }

    /**
     * @return bool|object
     */
    public function getControllerLoader()
    {
        return $this->getService('ControllerLoader');
    }

    /**
     * @param $controllerLoader
     * @return $this
     */
    public function setControllerLoader($controllerLoader)
    {
        return $this->addService('ControllerLoader', $controllerLoader);
    }

    /**
     * @return bool|object
     */
    public function getViewModel()
    {
        return $this->getService('ViewModel');
    }

    /**
     * @param ViewModel $viewModel
     * @return $this
     */
    public function setViewModel(ViewModel $viewModel)
    {
        return $this->addService('ViewModel', $viewModel);
    }

    /**
     * @return bool|object
     */
    public function getViewPluginManager()
    {
        return $this->getService('ViewPluginManager');
    }

    /**
     * @return bool|object
     */
    public function getView()
    {
        return $this->getService('View');
    }
}
