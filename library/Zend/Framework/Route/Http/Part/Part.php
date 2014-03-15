<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http\Part;

use Zend\Mvc\Router\Exception;
use Zend\Framework\Route\Assemble\AssembleInterface;
use Zend\Framework\Route\Http\EventInterface;
use Zend\Framework\Route\Manager\Manager as RoutePluginManager;
use Zend\Framework\Route\Manager\ConfigInterface;
use Zend\Framework\Route\Match\MatchInterface as RouteMatchInterface;
use Zend\Mvc\Router\Http\RouteInterface as HttpRouteInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteMatch;

class Part
    extends RoutePluginManager implements AssembleInterface, RouteMatchInterface
{
    /**
     * RouteMatchInterface to match.
     *
     * @var RouteMatchInterface
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
     * @param ConfigInterface $config
     * @param $route
     * @param $mayTerminate
     * @param array $childRoutes
     */
    public function __construct(ConfigInterface $config, $route, $mayTerminate, array $childRoutes = null)
    {
        parent::__construct($config);
        $this->route        = $route;
        $this->mayTerminate = $mayTerminate;
        $this->childRoutes  = $childRoutes;
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|string
     * @throws \Zend\Mvc\Router\Exception\RuntimeException
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
        foreach ($this->listeners as $route) {
            if ($route instanceof HttpRouteInterface) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     * @param null $pathOffset
     * @param array $options
     * @return null|RouteMatch|\Zend\Mvc\Router\RouteMatch
     */
    public function match(Request $request, $pathOffset = null, array $options = [])
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

            foreach ($this->listeners as $name => $route) {
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
     * @param EventInterface $event
     * @param null $options
     * @return RouteMatch
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        return $this->match($event->request(), $event->baseUrlLength(), $options);
    }
}
