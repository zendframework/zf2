<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Route;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Route\Event as Route;

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
    public function __construct($event = self::EVENT_APPLICATION, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $route = new Route;

        $route->setTarget($event->target())
              ->setRequest($this->request)
              ->setRouter($this->router);

        $this->em->__invoke($route);

        $routeMatch = $route->routeMatch();

        $this->sm->setRouteMatch($routeMatch);

        $event->setRouteMatch($routeMatch);
    }
}
