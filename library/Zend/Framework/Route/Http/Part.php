<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use ArrayObject;
use Zend\Framework\Route\RouteInterface;
use Zend\Mvc\Router\Exception;
use Zend\Framework\Route\PriorityList;
use Zend\Framework\Route\Manager\Listener as RoutePluginManager;
use Zend\Mvc\Router\Http\Query;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteMatch;

/**
 * Part route.
 */
class Part
    extends TreeRouteStack implements RouteInterface
{
    /**
     * RouteInterface to match.
     *
     * @var RouteInterface
     */
    protected $route;

    /**
     * Whether the route may terminate.
     *
     * @var bool
     */
    protected $mayTerminate;

    /**
     * Child routes.
     *
     * @var mixed
     */
    protected $childRoutes;

    /**
     * Create a new part route.
     *
     * @param  mixed              $route
     * @param  bool               $mayTerminate
     * @param  RoutePluginManager $rm
     * @param  array|null         $childRoutes
     * @param  ArrayObject|null   $prototypes
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($route, $mayTerminate, RoutePluginManager $rm, array $childRoutes = null, ArrayObject $prototypes = null)
    {
        $this->rm = $rm;

        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        if ($route instanceof self) {
            throw new Exception\InvalidArgumentException('Base route may not be a part route');
        }

        $this->route        = $route;
        $this->mayTerminate = $mayTerminate;
        $this->childRoutes  = $childRoutes;
        $this->prototypes   = $prototypes;
        $this->routes       = new PriorityList();
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    \Zend\Framework\Route\RouteInterface::match()
     * @param  Request      $request
     * @param  integer|null $pathOffset
     * @param  array        $options
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null, array $options = array())
    {
        if ($pathOffset === null) {
            $pathOffset = 0;
        }

        $match = $this->route->match($request, $pathOffset, $options);

        if ($match !== null && method_exists($request, 'getUri')) {
            if ($this->childRoutes !== null) {
                $this->addRoutes($this->childRoutes);
                $this->childRoutes = null;
            }

            $nextOffset = $pathOffset + $match->getLength();

            $uri        = $request->getUri();
            $pathLength = strlen($uri->getPath());

            if ($this->mayTerminate && $nextOffset === $pathLength) {
                $query = $uri->getQuery();
                if ('' == trim($query) || !$this->hasQueryChild()) {
                    return $match;
                }
            }

            foreach ($this->routes as $name => $route) {
                if (($subMatch = $route->match($request, $nextOffset, $options)) instanceof RouteMatch) {
                    if ($match->getLength() + $subMatch->getLength() + $pathOffset === $pathLength) {
                        return $match->merge($subMatch)->setMatchedRouteName($name);
                    }
                }
            }
        }

        return null;
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    \Zend\Framework\Route\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function assemble(array $params = array(), array $options = array())
    {
        if ($this->childRoutes !== null) {
            $this->addRoutes($this->childRoutes);
            $this->childRoutes = null;
        }

        $options['has_child'] = (isset($options['name']));

        $path   = $this->route->assemble($params, $options);
        $params = array_diff_key($params, array_flip($this->route->getAssembledParams()));

        if (!isset($options['name'])) {
            if (!$this->mayTerminate) {
                throw new Exception\RuntimeException('Part route may not terminate');
            } else {
                return $path;
            }
        }

        unset($options['has_child']);
        $options['only_return_path'] = true;
        $path .= parent::assemble($params, $options);

        return $path;
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    RouteInterface::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        // Part routes may not occur as base route of other part routes, so we
        // don't have to return anything here.
        return array();
    }

    /**
     * Is one of the child routes a query route?
     *
     * @return bool
     */
    protected function hasQueryChild()
    {
        foreach ($this->routes as $route) {
            if ($route instanceof Query) {
                return true;
            }
        }
        return false;
    }
}
