<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends EventListener
    implements FactoryInterface
{
    protected $name = MvcEvent::EVENT_ROUTE;

    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    public function __invoke(Event $event)
    {
        var_dump(__FILE__);
        $request    = $event->getRequest();
        $router     = $event->getRouter();
        $routeMatch = $router->match($request);

        if ($routeMatch instanceof RouteMatch) {
            $event->setRouteMatch($routeMatch);
        }
    }
}
