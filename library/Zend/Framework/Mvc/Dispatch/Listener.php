<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\Dispatch;

use Zend\Framework\Dispatch\Error\Event as DispatchErrorEvent;
use Zend\Framework\Dispatch\Event as DispatchEvent;
use Zend\Framework\Dispatch\Exception as DispatchException;
use Zend\Framework\Mvc\EventInterface;

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
    public function __construct($event = self::EVENT_MVC_APPLICATION, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $dispatch = new DispatchEvent;

        $dispatch->setTarget($event->target())
                 ->setServiceManager($sm);

        try {

            $em->__invoke($dispatch);

            $event->setResult($dispatch->getResult());

        } catch (DispatchException $exception) {

            $dispatch = new DispatchErrorEvent;

            $dispatch->setTarget($event->getTarget())
                     ->setException($exception->getException())
                     ->setControllerName($exception->getControllerName())
                     ->setControllerClass($exception->getControllerClass());

            $em->__invoke($dispatch);
        }
    }
}
