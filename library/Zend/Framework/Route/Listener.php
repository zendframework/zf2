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
use Zend\Mvc\Router\RouteMatch;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_ROUTE, $target = null, $priority = null)
    {
        $this->eventName = $event;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $request    = $event->getRequest();
        $router     = $event->getRouter();

        $routeMatch = $router->match($request);

        if ($routeMatch instanceof RouteMatch) {
            $event->setRouteMatch($routeMatch);
        }
    }
}
