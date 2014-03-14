<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http\Literal;

use Zend\Framework\Route\RouteInterface;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Literal route.
 */
class Literal implements RouteInterface
{
    /**
     * RouteInterface to match.
     *
     * @var string
     */
    protected $route;

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Create a new literal route.
     *
     * @param  string $route
     * @param  array  $defaults
     */
    public function __construct($route, array $defaults = array())
    {
        $this->route    = $route;
        $this->defaults = $defaults;
    }

    /**
     * @param Request $request
     * @param null $pathOffset
     * @return null|RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        $uri  = $request->getUri();
        $path = $uri->getPath();

        if ($pathOffset !== null) {
            if ($pathOffset >= 0 && strlen($path) >= $pathOffset && !empty($this->route)) {
                if (strpos($path, $this->route, $pathOffset) === $pathOffset) {
                    return new RouteMatch($this->defaults, strlen($this->route));
                }
            }

            return null;
        }

        if ($path === $this->route) {
            return new RouteMatch($this->defaults, strlen($this->route));
        }

        return null;
    }

    /**
     * @param $event
     * @return null|RouteMatch
     */
    public function __invoke($event)
    {
        return $this->match($event->request, $event->baseUrlLength);
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    \Zend\Framework\Route\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        return $this->route;
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    RouteInterface::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return array();
    }
}
