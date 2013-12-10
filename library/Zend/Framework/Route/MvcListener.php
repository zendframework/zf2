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
use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface;

class MvcListener extends EventListener implements FactoryInterface
{

    protected $name = 'mvc.application';

    public function createService(ServiceManagerInterface $sm)
    {
        return new static();
    }

    public function __invoke(EventInterface $event)
    {
        $em = $event->getEventManager();

        $routeEvent = new RouteEvent();

        $routeEvent->setTarget($event->getTarget())
                   ->setRequest($event->getRequest())
                   ->setRouter($event->getRouter());

        $em->trigger($routeEvent);
    }
}
