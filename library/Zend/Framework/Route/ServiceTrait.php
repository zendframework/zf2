<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Mvc\Router\RouteMatch;
use Zend\Framework\Route\RouteInterface as Router;

trait ServiceTrait
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @param $request
     * @return RouteMatch
     */
    public function match($request)
    {
        return $this->router->match($request);
    }

    /**
     * @return Router
     */
    public function router()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     * @return self
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return null|RouteMatch
     */
    public function routeMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }
}
