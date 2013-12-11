<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class Listener extends EventListener
{
    protected $name = MvcEvent::EVENT_ROUTE;


    public function __invoke(Event $event)
    {
        $request    = $event->getRequest();
        $router     = $event->getRouter();
        $routeMatch = $router->match($request);

        if ($routeMatch instanceof RouteMatch) {
            $event->setRouteMatch($routeMatch);
        }
    }
}
