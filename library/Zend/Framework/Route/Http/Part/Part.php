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
use Zend\Framework\Route\Manager\ManagerInterface as RouteManager;
use Zend\Mvc\Router\Http\RouteInterface as HttpRouteInterface;
use Zend\Framework\Route\RouteInterface;
use Zend\Uri\Http as Uri;
use Zend\Mvc\Router\Http\RouteMatch;

class Part
    implements
        AssembleInterface,
        PartInterface,
        RouteInterface
{
    /**
     * @var array
     */
    protected $childRoutes;

    /**
     * @var bool
     */
    protected $mayTerminate;

    /**
     * @var RouteManager
     */
    protected $rm;

    /**
     * @var
     */
    protected $route;

    /**
     * @param RouteManager $rm
     * @param $route
     * @param $mayTerminate
     * @param array $childRoutes
     */
    public function __construct(RouteManager $rm, $route, $mayTerminate, array $childRoutes = [])
    {
        $this->childRoutes  = $childRoutes;
        $this->mayTerminate = $mayTerminate;
        $this->rm           = $rm;
        $this->route        = $route;
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|string
     * @throws Exception\RuntimeException
     */
    public function assemble(array $params = [], array $options = [])
    {
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
        $path .= $this->rm->assemble($params, $options);

        return $path;
    }

    /**
     * @return array
     */
    public function getAssembledParams()
    {
        // Part routes may not occur as base route of other part routes, so we
        // don't have to return anything here.
        return [];
    }

    /**
     * Is one of the child routes a query route?
     *
     * @return bool
     */
    protected function hasQueryChild()
    {
        foreach ($this->childRoutes as $route) {
            if ($route instanceof HttpRouteInterface) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Uri $uri
     * @param null $pathOffset
     * @param array $options
     * @return null|RouteMatch
     */
    public function match(Uri $uri, $pathOffset = null, array $options = [])
    {
        if ($pathOffset === null) {
            $pathOffset = 0;
        }

        $match = $this->route->match($uri, $pathOffset, $options);

        $nextOffset = $pathOffset + $match->getLength();

        $pathLength = strlen($uri->getPath());

        if ($this->mayTerminate && $nextOffset === $pathLength) {
            $query = $uri->getQuery();
            if ('' == trim($query) || !$this->hasQueryChild()) {
                return $match;
            }
        }

        foreach ($this->childRoutes as $name => $route) {

            $route = $this->rm->route($name, $route);

            if (($subMatch = $route->match($uri, $nextOffset, $options)) instanceof RouteMatch) {
                if ($match->getLength() + $subMatch->getLength() + $pathOffset === $pathLength) {
                    return $match->merge($subMatch)->setMatchedRouteName($name);
                }
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->route->name();
    }

    /**
     * @param EventInterface $event
     * @param null $options
     * @return RouteMatch
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        return $this->match($event->uri(), $event->pathOffset(), (array) $options);
    }
}
