<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use ArrayObject;
use Zend\Framework\EventManager\EventManagerInterface as EventManager;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Mvc\Exception\InvalidControllerException;
use Zend\Stdlib\ArrayUtils;
use Zend\Framework\MvcEvent;

use Zend\Framework\Controller\DispatchEvent as ControllerDispatchEvent;
use Zend\Framework\Dispatch\Event as DispatchEvent;
use Zend\Framework\Dispatch\Exception as DispatchException;
use Zend\Framework\Dispatch\ErrorEvent as DispatchErrorEvent;


use Zend\View\Model\ViewModel;
use Zend\Framework\Application;

use Exception;

/**
 * Default dispatch listener
 *
 * Pulls controllers from the service manager's "ControllerLoader" service.
 *
 * If the controller cannot be found a "404" result is set up. Otherwise it
 * will continue to try to load the controller.
 *
 * If the controller is not dispatchable it sets up a "404" result. In case
 * of any other exceptions it trigger the "dispatch.error" event in an attempt
 * to return a 500 status.
 *
 * If the controller subscribes to InjectApplicationEventInterface, it injects
 * the current MvcEvent into the controller.
 *
 * It then calls the controller's "dispatch" method, passing it the request and
 * response. If an exception occurs, it triggers the "dispatch.error" event,
 * in an attempt to return a 500 status.
 *
 * The return value of dispatching the controller is placed into the result
 * property of the MvcEvent, and returned.
 */
class Listener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    public function attach(EventManager $em)
    {
        $this->listeners[] = $em->attach(new CallbackListener(array($this, 'onDispatch'), MvcEvent::EVENT_DISPATCH));
    }

    public function detach(EventManager $em)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($em->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onDispatch(DispatchEvent $event)
    {
        $em = $event->getEventManager();

        $routeMatch = $event->getRouteMatch();

        $controllerName = $routeMatch->getParam('controller', 'not-found');

        $controllerLoader = $event->getControllerLoader();

        $controller = $controllerLoader->get(new ServiceRequest($controllerName));

        $request  = $event->getRequest();
        $response = $event->getResponse();

        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($event);
        }

        $controller->setTarget($controller);

        $em->attach($controller);

        $viewModel = new ViewModel();

        try {

            $dispatchEvent = new ControllerDispatchEvent();
            $dispatchEvent->setTarget($controller)
                          ->setController($controller)
                          ->setResponse($response)
                          ->setViewModel($viewModel);

            $em->trigger($dispatchEvent);

            $event->setResponse($dispatchEvent->getResponse())
                  ->setResult($dispatchEvent->getResult())
                  ->setViewModel($dispatchEvent->getViewModel());

        } catch (Exception $exception) {

            $dispatchException = new DispatchException();

            $dispatchException->setControllerName($controllerName)
                              ->setControllerClass(get_class($controller))
                              ->setException($exception);

            throw $dispatchException;
        }
    }

    public function __invoke(ServiceManager $sm)
    {
        return $this;
    }
}
