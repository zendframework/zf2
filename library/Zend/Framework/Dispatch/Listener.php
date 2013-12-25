<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Exception;
use Zend\Framework\Controller\Dispatch\Event as ControllerDispatchEvent;
use Zend\Framework\Dispatch\Exception as DispatchException;
use Zend\Framework\EventManager\EventInterface as Event;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_DISPATCH, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     * @throws DispatchException
     */
    public function __invoke(EventInterface $event)
    {
        $em = $event->getEventManager();
        $cm = $event->getControllerManager();
        $rm = $event->getRouteMatch();
        $sm = $event->getServiceManager();
        $vm = $event->getViewModel();

        $controllerName = $rm->getParam('controller', 'not-found');

        $controller = $cm->getController( $controllerName );

        $em->push($controller);

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
                              ->setControllerClass(get_class($controller))
                              ->setException($exception);

            throw $dispatchException;
        }
    }
}
