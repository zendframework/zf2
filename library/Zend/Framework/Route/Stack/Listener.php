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
    implements ListenerInterface, RouteInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     *
     */
    public function __construct(PriorityList $routes = null)
    {
        if (!$routes) {
            $routes = new PriorityList;
        }
        $this->routes = $routes;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function trigger(EventInterface $event)
    {
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
