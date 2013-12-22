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
use Zend\Framework\EventManager\ManagerInterface as EventManager;
//use Zend\Framework\ServiceManager\ServiceManagerInterface;
use Zend\Framework\View\Manager as ViewManager;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\View\Renderer\Renderer as ViewRenderer;

//use Zend\Console\Request as Request;
use Zend\Http\PhpEnvironment\Request as Request;

//use Zend\Console\Response as Response;
use Zend\Http\PhpEnvironment\Response as Response;

use Zend\Mvc\Router\RouteStackInterface as Router;

use Zend\Mvc\Controller\ControllerManager as ControllerLoader;

use Zend\Mvc\Router\Http\RouteMatch as RouteMatch;

use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;

use Zend\Mvc\Router\RoutePluginManager as RoutePluginManager;


trait ApplicationServiceTrait
{
    /**
     * @var ApplicationServiceManager
     */
    protected $sm;

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
        return $this->addService('ServiceManager', $this->sm = $sm);
    }

    /**
     * @return ViewManager
     */
    public function getViewManager()
    {
        return $this->getService('View\Manager');
    }

    /**
     * @param ViewManager $vm
     * @return $this
     */
    public function setViewManager(ViewManager $vm)
    {
        return $this->addService('View\Manager', $vm);
    }

    /**
     * @return array
     */
    public function getApplicationConfig()
    {
        return $this->getService('ApplicationConfig');
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setApplicationConfig(array $config)
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
     * @return bool|ViewRenderer
     */
    public function getViewRenderer()
    {
        return $this->getService('View\Renderer');
    }

    /**
     * @param ViewRenderer $renderer
     * @return $this
     */
    public function setViewRenderer(ViewRenderer $renderer)
    {
        return $this->addService('View\Renderer', $renderer);
    }

    /**
     * @return bool|ViewResolver
     */
    public function getViewResolver()
    {
        return $this->getService('View\Resolver');
    }

    /**
     * @param ViewResolver $resolver
     * @return $this
     */
    public function setViewResolver(ViewResolver $resolver)
    {
        return $this->addService('View\Resolver', $resolver);
    }

    /**
     * @return bool|Request
     */
    public function getRequest()
    {
        return $this->getService('Request');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
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
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        return $this->addService('Response', $response);
    }

    /**
     * @return bool|Router
     */
    public function getRouter()
    {
        return $this->getService('Router');
    }

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        return $this->addService('Router', $router);
    }

    /**
     * @return bool|RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getService('Route\Match');
    }

    /**
     * @param RouteMatch $routeMatch
     * @return $this
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        return $this->addService('Route\Match', $routeMatch);
    }

    /**
     * @return bool|ControllerLoader
     */
    public function getControllerLoader()
    {
        return $this->getService('ControllerLoader');
    }

    /**
     * @param ControllerLoader $controllerLoader
     * @return $this
     */
    public function setControllerLoader(ControllerLoader $controllerLoader)
    {
        return $this->addService('ControllerLoader', $controllerLoader);
    }

    /**
     * @return bool|ViewModel
     */
    public function getViewModel()
    {
        return $this->getService('View\Model');
    }

    /**
     * @param ViewModel $viewModel
     * @return $this
     */
    public function setViewModel(ViewModel $viewModel)
    {
        return $this->addService('View\Model', $viewModel);
    }

    /**
     * @return bool|ViewPluginManager
     */
    public function getViewPluginManager()
    {
        return $this->getService('View\Plugin\Manager');
    }

    /**
     * @return bool|View
     */
    public function getView()
    {
        return $this->getService('View');
    }

    /**
     * @return bool|ControllerPluginManager
     */
    public function getControllerPluginManager()
    {
        return $this->getService('ControllerPluginManager');
    }

    /**
     * @return bool|RoutePluginManager
     */
    public function getRoutePluginManager()
    {
        return $this->getService('RoutePluginManager');
    }
}
