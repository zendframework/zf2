<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Dispatch\Event as DispatchEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\CreateServiceTrait as CreateService;

class MvcListener
    extends EventListener
{
    /**
     * @var string
     */
    protected $eventName = MvcEvent::EVENT_NAME;

    /**
     *
     */
    use CreateService;

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $dispatch = new DispatchEvent;

        $dispatch->setEventTarget($event->getEventTarget())
                 ->setServiceManager($sm);

        try {

            $em->trigger($dispatch);

            $event->setResult($dispatch->getResult());

        } catch (DispatchException $exception) {

            $dispatch = new DispatchErrorEvent;

            $dispatch->setEventTarget($event->getApplication())
                     ->setException($exception->getPrevious())
                     ->setController($exception->getControllerName())
                     ->setControllerClass($exception->getControllerClass());

            $em->trigger($dispatch);
        }
    }
}
