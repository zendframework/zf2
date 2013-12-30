<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

//use Zend\Console\Request as Request;
//use Zend\Console\Response as Response;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\View\Config as ViewConfig;
use Zend\Framework\View\Manager as ViewManager;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\Listener as View;
use Zend\Framework\View\Plugin\Manager as ViewPluginManager;
use Zend\Http\PhpEnvironment\Request as Request;
use Zend\Http\PhpEnvironment\Response as Response;
use Zend\Mvc\Controller\ControllerManager as ControllerManager;
use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RoutePluginManager as RoutePluginManager;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\View\Renderer\RendererInterface as ViewRenderer;
use Zend\View\Resolver\ResolverInterface as ViewResolver;

trait ServicesTrait
{
    /**
     * @return array
     */
    public function applicationConfig()
    {
        return $this->service('ApplicationConfig');
    }

    /**
     * @param array $config
     * @return self
     */
    public function setApplicationConfig(array $config)
    {
        return $this->add('ApplicationConfig', $config);
    }

    /**
     * @return mixed
     */
    public function eventManager()
    {
        return $this->service('EventManager');
    }

    /**
     * @param EventManager $em
     * @return $this
     */
    public function setEventManager(EventManager $em)
    {
        $this->add('EventManager', $em);
        return $this;
    }

    /**
     * @return ViewManager
     */
    public function viewManager()
    {
        return $this->service('View\Manager');
    }

    /**
     * @param ViewManager $vm
     * @return self
     */
    public function setViewManager(ViewManager $vm)
    {
        return $this->add('View\Manager', $vm);
    }

    /**
     * @return ViewConfig
     */
    public function viewConfig()
    {
        return $this->viewManager()->viewConfig();
    }

    /**
     * @return bool|ViewRenderer
     */
    public function viewRenderer()
    {
        return $this->service('View\Renderer');
    }

    /**
     * @param ViewRenderer $renderer
     * @return self
     */
    public function setViewRenderer(ViewRenderer $renderer)
    {
        return $this->add('View\Renderer', $renderer);
    }

    /**
     * @return bool|ViewResolver
     */
    public function viewResolver()
    {
        return $this->service('View\Resolver');
    }

    /**
     * @param ViewResolver $resolver
     * @return self
     */
    public function setViewResolver(ViewResolver $resolver)
    {
        return $this->add('View\Resolver', $resolver);
    }

    /**
     * @return bool|Request
     */
    public function request()
    {
        return $this->service('Request');
    }

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request)
    {
        return $this->add('Request', $request);
    }

    /**
     * @return bool|object
     */
    public function response()
    {
        return $this->service('Response');
    }

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response)
    {
        return $this->add('Response', $response);
    }

    /**
     * @return bool|Router
     */
    public function router()
    {
        return $this->service('Router');
    }

    /**
     * @param Router $router
     * @return self
     */
    public function setRouter(Router $router)
    {
        return $this->add('Router', $router);
    }

    /**
     * @return bool|RouteMatch
     */
    public function routeMatch()
    {
        return $this->service('Route\Match');
    }

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        return $this->add('Route\Match', $routeMatch);
    }

    /**
     * @return bool|ControllerManager
     */
    public function controllerManager()
    {
        return $this->service('Controller\Manager');
    }

    /**
     * @param ControllerManager $cm
     * @return self
     */
    public function setControllerManager(ControllerManager $cm)
    {
        return $this->add('Controller\Manager', $cm);
    }

    /**
     * @return bool|ViewModel
     */
    public function viewModel()
    {
        return $this->service('View\Model');
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel)
    {
        return $this->add('View\Model', $viewModel);
    }

    /**
     * @return bool|ViewPluginManager
     */
    public function viewPluginManager()
    {
        return $this->service('View\Plugin\Manager');
    }

    /**
     * @return bool|View
     */
    public function view()
    {
        return $this->service('View');
    }

    /**
     * @param View $view
     * @return self
     */
    public function setView(View $view)
    {
        return $this->add('View', $view);
    }

    /**
     * @return bool|ControllerPluginManager
     */
    public function controllerPluginManager()
    {
        return $this->service('Controller\Plugin\Manager');
    }

    /**
     * @return bool|RoutePluginManager
     */
    public function routePluginManager()
    {
        return $this->service('RoutePluginManager');
    }
}
