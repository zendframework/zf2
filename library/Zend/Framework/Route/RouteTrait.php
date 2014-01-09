<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Framework\Route\PriorityList;
use Zend\Stdlib\ArrayUtils;

trait RouteTrait
{

    /**
     * Stack containing all routes.
     *
     * @var PriorityList
     */
    protected $routes;

    /**
     * addRoutes(): defined by RouteStackInterface interface.
     *
     * @see    RouteStackInterface::addRoutes()
     * @param  array|Traversable $routes
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function addRoutes($routes)
    {
        if (!is_array($routes) && !$routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addRoutes expects an array or Traversable set of routes');
        }

        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * addRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStackInterface::addRoute()
     * @param  string  $name
     * @param  mixed   $route
     * @param  int $priority
     * @return self
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        if ($priority === null && isset($route->priority)) {
            $priority = $route->priority;
        }

        $this->routes->insert($name, $route, $priority);

        return $this;
    }

    /**
     * assemble(): defined by RouteInterface interface.
     *
     * @see    \Zend\Framework\Route\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function assemble(array $params = array(), array $options = array())
    {
        if (!isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $route = $this->routes->get($options['name']);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $options['name']));
        }

        unset($options['name']);

        return $route->assemble(array_merge($this->defaultParams, $params), $options);
    }

    /**
     * Get a route by name
     *
     * @param string $name
     * @return RouteInterface the route
     */
    public function getRoute($name)
    {
        return $this->routes->get($name);
    }

    /**
     * Get the added routes
     *
     * @return Traversable list of all routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Check if a route with a specific name exists
     *
     * @param  string $name
     * @return bool true if route exists
     */
    public function hasRoute($name)
    {
        return $this->routes->get($name) !== null;
    }

    /**
     * removeRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStackInterface::removeRoute()
     * @param  string $name
     * @return self
     */
    public function removeRoute($name)
    {
        $this->routes->remove($name);
        return $this;
    }

    /**
     * Create a route from array specifications.
     *
     * @param  array|Traversable $specs
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    protected function routeFromArray($specs)
    {
        if ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        if (!isset($specs['type'])) {
            throw new Exception\InvalidArgumentException('Missing "type" option');
        } elseif (!isset($specs['options'])) {
            $specs['options'] = array();
        }

        $route = $this->getRoutePluginManager()->get($specs['type'], $specs['options']);

        if (isset($specs['priority'])) {
            $route->priority = $specs['priority'];
        }

        return $route;
    }

    /**
     * setRoutes(): defined by RouteStackInterface interface.
     *
     * @param  array|Traversable $routes
     * @return self
     */
    public function setRoutes($routes)
    {
        $this->routes->clear();
        $this->addRoutes($routes);
        return $this;
    }
}
