<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Stack;

use Zend\Framework\Route\Manager\ServiceTrait as RouteManager;
use Zend\Framework\Route\ParamTrait as Param;
use Zend\Framework\Route\RouteTrait as Route;
use Zend\Mvc\Router\RouteMatch as RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

trait ListenerTrait
{
    /**
     *
     */
    use Param,
        Route,
        RouteManager;

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    \Zend\Framework\Route\RouteInterface::match()
     * @param  Request $request
     * @return RouteMatch|null
     */
    public function match(Request $request)
    {
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
