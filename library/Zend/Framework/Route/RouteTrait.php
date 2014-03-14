<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Traversable;
use Zend\Framework\Route\Manager\ServiceTrait as RouteManager;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;

trait RouteTrait
{
    /**
     *
     */
    use RouteManager;

    /**
     * Stack containing all routes.
     *
     * @var PriorityList
     */
    protected $routes;

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
}
