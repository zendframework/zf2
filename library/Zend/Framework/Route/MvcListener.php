<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Route\Event as RouteEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\EventManager\Listener as ParentListener;
use Zend\Framework\ServiceManager\FactoryInterface;

class MvcListener
    extends ParentListener
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return MvcListener
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();

        $route = new RouteEvent;

        $route->setEventTarget($event->getEventTarget())
              ->setServiceManager($event->getServiceManager())
              ->setRequest($event->getRequest())
              ->setRouter($event->getRouter());

        $em->trigger($route);
    }
}
