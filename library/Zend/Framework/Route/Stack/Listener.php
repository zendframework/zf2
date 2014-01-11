<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Stack;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Route\RouteInterface;
use Zend\Framework\Route\PriorityList;
use Zend\Mvc\Router\Http\RouteMatch;

/**
 * Simple route stack implementation.
 */
class Listener
    implements ListenerInterface, EventListenerInterface, RouteInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param null $target
     * @param null $priority
     */
    public function __construct($event = self::EVENT_ROUTE_STACK, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
        $this->routes = new PriorityList;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        var_dump(__FILE__);
        $request    = $event->target();

        foreach ($this->routes as $name => $route) {
            if (($match = $route->match($request)) instanceof RouteMatch) {
                $match->setMatchedRouteName($name);

                foreach ($this->defaultParams as $paramName => $value) {
                    if ($match->getParam($paramName) === null) {
                        $match->setParam($paramName, $value);
                    }
                }

                return $match;
            }
        }

        return null;
    }
}
