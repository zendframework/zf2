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
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class MvcListener
    extends EventListener
    implements FactoryInterface
{

    protected $name = 'mvc.application';

    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    public function __invoke(Event $event)
    {
        var_dump(__FILE__);
        $em = $event->getEventManager();

        $dispatch = new DispatchEvent;

        $dispatch->setTarget($event->getTarget())
                 ->setApplication($event->getApplication())
                 ->setServiceManager($event->getServiceManager())
                 ->setRouteMatch($event->getRouteMatch())
                 ->setEventManager($event->getEventManager())
                 ->setRequest($event->getRequest())
                 ->setResponse($event->getResponse())
                 ->setControllerLoader($event->getControllerLoader())
                 ->setViewConfig($event->getViewConfig())
                 ->setViewManager($event->getViewManager())
                 ->setViewModel($event->getViewModel())
                 ->setViewResolver($event->getViewResolver())
                 ->setViewPluginManager($event->getViewPluginManager())
                 ->setView($event->getView());

        try {

            $em->trigger($dispatch);

        } catch (DispatchException $exception) {

            $dispatch = new DispatchErrorEvent;

            $dispatch->setTarget($event->getApplication())
                     ->setException($exception->getException())
                     ->setController($exception->getControllerName())
                     ->setControllerClass($exception->getControllerClass());

            $em->trigger($dispatch);
        }
    }
}
