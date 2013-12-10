<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\EventManager\EventManagerAwareInterface;
use Zend\Framework\EventManager\EventManagerInterface;
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Stdlib\ResponseInterface as Response;

use Zend\ModuleManager;
use Zend\Framework\ServiceManager\Config as ServiceManagerConfig;
use Zend\Framework\ServiceManager\ServiceRequest;

use Zend\Framework\ApplicationInterface;
use Zend\Framework\Bootstrap\Event as BootstrapEvent;
use Zend\Framework\Route\Event as RouteEvent;
use Zend\Framework\Dispatch\Event as DispatchEvent;
use Zend\Framework\Dispatch\ErrorEvent as DispatchErrorEvent;
use Zend\Framework\Response\Event as ResponseEvent;
use Zend\Framework\Render\Event as RenderEvent;
use Zend\Framework\Finish\Event as FinishEvent;

use Zend\Framework\Dispatch\Exception as DispatchException;
use Zend\View\Model\ViewModel;

use Exception;

class Application implements
    ApplicationInterface
{

    const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    const ERROR_EXCEPTION                  = 'error-exception';
    const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';

    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_DISPATCH_ERROR = 'dispatch.error';
    const EVENT_FINISH         = 'finish';
    const EVENT_RENDER         = 'render';
    const EVENT_RENDER_ERROR   = 'render.error';
    const EVENT_ROUTE          = 'route';

    protected $config;

    protected $event;

    protected $em;

    protected $request;

    protected $response;

    protected $router;

    protected $controllerLoader;

    protected $sm;

    protected $bootstrapEvent;

    protected $viewModel;

    public function __construct(ServiceManager $sm)
    {
        $this->sm               = $sm;
        $this->config           = $sm->get(new ServiceRequest('ApplicationConfig'));
        $this->em               = $sm->get(new ServiceRequest('EventManager'));
        $this->request          = $sm->get(new ServiceRequest('Request'));
        $this->response         = $sm->get(new ServiceRequest('Response'));
        $this->router           = $sm->get(new ServiceRequest('Router'));
        $this->controllerLoader = $sm->get(new ServiceRequest('ControllerLoader'));
        $this->viewModel        = new ViewModel;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getControllerLoader()
    {
        return $this->controllerLoader;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->em;
    }

    /**
     * @return ViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * @param array $config
     * @return mixed
     */
    public static function init($config = array())
    {
        $sm = new ServiceManager(new ServiceManagerConfig($config['service_manager']));

        $sm->add('ApplicationConfig', $config);

        //$mm = $sm->get(new ServiceRequest('ModuleManager'));
        //$mm->loadModules();

        $application = new static($sm);

        $sm->add('Application', $application);

        $em = $application->getEventManager();

        $listeners = $config['mvc_listeners'];

        foreach($listeners as $l) {
            $em->attach($sm->get(new ServiceRequest($l)));
        }

        return $application;
    }

    /**
     * @return $this|mixed|Application|Response|ResponseInterface
     */
    public function run()
    {
        $em = $this->em;

        $bootstrapEvent = new BootstrapEvent();

        $bootstrapEvent->setTarget($this)
                       ->setApplication($this)
                       ->setServiceManager($this->sm)
                       ->setRequest($this->request)
                       ->setResponse($this->response)
                       ->setRouter($this->router)
                       ->setControllerLoader($this->controllerLoader)
                       ->setViewModel($this->viewModel);

        $em->trigger($bootstrapEvent);

        $request          = $bootstrapEvent->getRequest();
        $router           = $bootstrapEvent->getRouter();
        $response         = $bootstrapEvent->getResponse();
        $controllerLoader = $bootstrapEvent->getControllerLoader();
        $viewModel        = $bootstrapEvent->getViewModel();

        $routeEvent = new RouteEvent();

        $routeEvent->setTarget($this)
                   ->setRequest($request)
                   ->setRouter($router);

        $em->trigger($routeEvent);

        $routeMatch = $routeEvent->getRouteMatch();

        $dispatchEvent = new DispatchEvent();
        $dispatchEvent->setTarget($this)
                      ->setRouteMatch($routeMatch)
                      ->setEventManager($em)
                      ->setRequest($request)
                      ->setResponse($response)
                      ->setControllerLoader($controllerLoader)
                      ->setViewModel($viewModel);

        try {

            $em->trigger($dispatchEvent);

            $request   = $dispatchEvent->getRequest();
            $response  = $dispatchEvent->getResponse();
            $viewModel = $dispatchEvent->getViewModel();

        } catch (DispatchException $exception) {
            $errorEvent = new DispatchErrorEvent();

            $errorEvent->setTarget($this)
                       ->setException($exception->getException())
                       ->setController($exception->getControllerName())
                       ->setControllerClass($exception->getControllerClass());

            $em->trigger($errorEvent);

            $request   = $errorEvent->getRequest();
            $response  = $errorEvent->getResponse();
            $viewModel = $errorEvent->getViewModel();
        }

        $renderEvent = new RenderEvent();

        $renderEvent->setTarget($this)
                    ->setApplication($this)
                    ->setRequest($request)
                    ->setResponse($response)
                    ->setViewModel($viewModel);

        $em->trigger($renderEvent);

        $finishEvent = new FinishEvent();
        $finishEvent->setTarget($this)
                    ->setResponse($renderEvent->getResponse());

        $em->trigger($finishEvent);

        $responseEvent = new ResponseEvent();

        $responseEvent->setTarget($this)
                      ->setResponse($response);

        $em->trigger($responseEvent);

        return $this;
    }
}
