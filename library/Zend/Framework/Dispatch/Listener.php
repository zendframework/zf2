<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Controller\DispatchEvent as ControllerDispatchEvent;
use Zend\Framework\Dispatch\Exception as DispatchException;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceRequest;

use Exception;

class Listener
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_DISPATCH;

    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    /**
     * @param Event $event
     * @return void
     * @throws DispatchException
     */
    public function __invoke(Event $event)
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

        $vm = $event->getViewManager();

        try {

            $dispatchEvent = new ControllerDispatchEvent;

            $dispatchEvent->setTarget($controller)
                          ->setServiceManager($event->getServiceManager())
                          ->setRouteMatch($event->getRouteMatch())
                          ->setController($controller)
                          ->setResponse($response)
                          //->setViewManager($vm)
                          ->setViewModel($event->getViewModel());

            $em->trigger($dispatchEvent);

            $event->setResponse($dispatchEvent->getResponse())
                  ->setResult($dispatchEvent->getResult())
                  ->setViewManager($dispatchEvent->getViewManager());

        } catch (Exception $exception) {

            $dispatchException = new DispatchException();

            $dispatchException->setControllerName($controllerName)
                              ->setControllerClass(get_class($controller))
                              ->setException($exception);

            throw $dispatchException;
        }
    }
}
