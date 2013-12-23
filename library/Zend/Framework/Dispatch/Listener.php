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
use Zend\Framework\Controller\DispatchEvent as ControllerDispatchEvent;
use Zend\Framework\Dispatch\Exception as DispatchException;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $eventName = self::EVENT_DISPATCH;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = self::WILDCARD;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $eventPriority = self::DEFAULT_PRIORITY;

    /**
     * @var
     */
    protected $dispatch;

    /**
     * @param Event $event
     * @return mixed|void
     * @throws DispatchException
     */
    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();
        $cm = $event->getControllerManager();
        $rm = $event->getRouteMatch();
        $sm = $event->getServiceManager();
        $vm = $event->getViewModel();

        $controllerName = $rm->getParam('controller', 'not-found');

        $controller = $cm->getController( $controllerName );

        $em->attach($controller);

        $dispatch = new ControllerDispatchEvent;

        $dispatch->setEventTarget($controller)
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
