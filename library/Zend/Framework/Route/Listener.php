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
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $eventName = self::EVENT_ROUTE;

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
     * @param Event $event
     * @return void
     */
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
