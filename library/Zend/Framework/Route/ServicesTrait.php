<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Mvc\Router\RoutePluginManager as RoutePluginManager;

trait ServicesTrait
{
    /**
     * @return bool|RoutePluginManager
     */
    public function routePluginManager()
    {
        return $this->service('RoutePluginManager');
    }

    /**
     * @return bool|Router
     */
    public function router()
    {
        return $this->service('Router');
    }

    /**
     * @param Router $router
     * @return self
     */
    public function setRouter(Router $router)
    {
        return $this->add('Router', $router);
    }

    /**
     * @return bool|RouteMatch
     */
    public function routeMatch()
    {
        return $this->service('Route\Match');
    }

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        return $this->add('Route\Match', $routeMatch);
    }
}
