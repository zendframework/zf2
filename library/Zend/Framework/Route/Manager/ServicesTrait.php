<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Mvc\Router\RouteMatch;
use Zend\Framework\Route\RouteInterface as Router;

trait ServicesTrait
{
    /**
     * @return ManagerInterface
     */
    public function routeManager()
    {
        return $this->sm->get('Route\Manager');
    }

    /**
     * @return Router
     */
    public function router()
    {
        return $this->sm->get('Router');
    }

    /**
     * @return null|RouteMatch
     */
    public function routeMatch()
    {
        return $this->sm->get('Route\Match');
    }

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        return $this->sm->add('Route\Match', $routeMatch);
    }
}
