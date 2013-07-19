<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Router\Http;

use Zend\Mvc\Router\RouteMatch as BaseRouteMatch;

/**
 * Part route match.
 */
class RouteMatch extends BaseRouteMatch
{
    /**
     * Length of the matched path.
     *
     * @var int
     */
    protected $length;

    /**
     * Create a part RouteMatch with given parameters and length.
     *
     * @param  array   $params
     * @param  int $length
     */
    public function __construct(array $params, $length = 0)
    {
        parent::__construct($params);

        $this->length = $length;
    }

    /**
     * Sets or prepends a route name
     *
     * @see    BaseRouteMatch::setMatchedRouteName()
     * @see    prependMatchedRouteName()
     * @param  string $name the route to set
     * @return RouteMatch
     * @deprecated
     */
    public function setMatchedRouteName($name)
    {
        return $this->prependMatchedRouteName($name);
    }

    /**
     * Prepend a route $name to an existing matched route name
     *
     * If no route name has been matched, it will be set to the given $name.
     *
     * @param  string $name the route to prepend
     * @return RouteMatch
     */
    public function prependMatchedRouteName($name)
    {
        if (is_null($this->matchedRouteName)) {
            return parent::setMatchedRouteName($name);
        } else {
            $this->matchedRouteName = $name . '/' . $this->matchedRouteName;
        }

        return $this;
    }

    /**
     * Merge parameters from another match.
     *
     * @param  RouteMatch $match
     * @return RouteMatch
     */
    public function merge(RouteMatch $match)
    {
        $this->params  = array_merge($this->params, $match->getParams());
        $this->length += $match->getLength();

        $this->matchedRouteName = $match->getMatchedRouteName();

        return $this;
    }

    /**
     * Get the matched path length.
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}
