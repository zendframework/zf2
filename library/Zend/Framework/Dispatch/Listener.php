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

use Zend\Framework\ServiceManager\CreateServiceTrait as CreateService;

use Exception;

class Listener
    extends EventListener
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_DISPATCH;

    /**
     * @var
     */
    protected $dispatch;

    /**
     *
     */
    use CreateService;

    /**
     * @param Event $event
     * @return void
     * @throws DispatchException
     */
    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();
        $cm = $event->getControllerLoader();
        $rm = $event->getRouteMatch();
        $sm = $event->getServiceManager();
        $vm = $event->getViewModel();

        $controllerName = $rm->getParam('controller', 'not-found');

        $controller = $cm->getController( $controllerName );

        $controller->setTarget($controller);

        $em->attach($controller);

        $dispatch = new ControllerDispatchEvent;

        $dispatch->setTarget($controller)
                 ->setServiceManager($sm)
                 ->setController($controller)
                 ->setViewModel($vm);

        try {

            $em->trigger($dispatch);

            $event->setResponse($dispatch->getResponse())
                  ->setResult($dispatch->getResult())
                  ->setViewManager($dispatch->getViewManager());

        } catch (Exception $exception) {

            $dispatchException = new DispatchException;

            $dispatchException->setControllerName($controllerName)
                              ->setControllerClass(get_class($controller));

            throw $dispatchException;
        }
    }
}
