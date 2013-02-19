<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Hostname route.
 *
 * @see        http://guides.rubyonrails.org/routing.html
 */
class Hostname implements RouteInterface
{
    /**
     * RouteInterface to match.
     *
     * @var array
     */
    protected $route;

    /**
     * Constraints for parameters.
     *
     * @var array
     */
    protected $constraints;

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * List of assembled parameters.
     *
     * @var array
     */
    protected $assembledParams = array();

    /**
     * Create a new hostname route.
     *
     * @param  string $route
     * @param  array  $constraints
     * @param  array  $defaults
     */
    public function __construct($route, array $constraints = array(), array $defaults = array())
    {
        $this->route       = explode('.', $route);
        $this->constraints = $constraints;
        $this->defaults    = $defaults;
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::factory()
     * @param  array|Traversable $options
     * @throws \Zend\Mvc\Router\Exception\InvalidArgumentException
     * @return Hostname
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['constraints'])) {
            $options['constraints'] = array();
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($options['route'], $options['constraints'], $options['defaults']);
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri             = $request->getUri();
        $hostname        = array_reverse(explode('.', $uri->getHost()));
        $params          = array();
        $optionalSegment = false;

        // matching right-to-left
        foreach (array_reverse($this->route) as $index => $routePart) {
            if (preg_match('(^\[?:(?P<name>[^\]]+)\]?$)', $routePart, $matches)) {
                if ('[' == substr($routePart, 0, 1) && ']'== substr($routePart, -1)) {
                    // now should only see optional segments moving left
                    $optionalSegment = true;
                } elseif ($optionalSegment) {
                    // Non-optional segment found left of optional
                    return null;
                }
                if (isset($this->constraints[$matches['name']]) && !preg_match('(^' . $this->constraints[$matches['name']] . '$)', $hostname[$index])) {
                    // constraint violation
                    return null;
                }
                if (!isset($hostname[$index])) {
                    if (!$optionalSegment) {
                        // not enough hostname pieces
                        return null;
                    }
                    if ($optionalSegment) {
                        // no-more host pieces, check for optional segments in route
                        continue;
                    }
                }

                $params[$matches['name']] = $hostname[$index];
            } elseif ($optionalSegment) {
                // Literal segment found left of optional
                return null;
            } elseif (!isset($hostname[$index]) || $hostname[$index] !== $routePart) {
                // Not enough hostname pieces or literal mismatch
                return null;
            }
        }

        if (!$optionalSegment && count($hostname) !== count($this->route)) {
            // only routes w/o optional components can have host parts !== route parts
            return null;
        }
        if ($optionalSegment && count($hostname) > count($this->route)) {
            // even with optional components host parts cannot be > route parts
            return null;
        }

        return new RouteMatch(array_merge($this->defaults, $params));
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $mergedParams          = array_merge($this->defaults, $params);
        $this->assembledParams = array();
        $optionalSegment       = false;

        if (isset($options['uri'])) {
            $parts = array();

            // assembling right-to-left
            foreach (array_reverse($this->route) as $routePart) {
                if (preg_match('(^\[?:(?P<name>[^\]]+)\]?$)', $routePart, $matches)) {
                    if ('[' == substr($routePart, 0, 1) && ']'== substr($routePart, -1)) {
                        $optionalSegment = true;
                    } elseif ($optionalSegment) {
                        throw new Exception\InvalidArgumentException('Non-optional segment found left of optional');
                    } elseif (!isset($mergedParams[$matches['name']])) {
                        throw new Exception\InvalidArgumentException(sprintf('Missing parameter "%s"', $matches['name']));
                    }

                    if (isset($mergedParams[$matches['name']])) {
                        $parts[] = $mergedParams[$matches['name']];
                        $this->assembledParams[] = $matches['name'];
                    }
                } else {
                    $parts[] = $routePart;
                }
            }

            $options['uri']->setHost(implode('.', array_reverse($parts)));
        }

        // A hostname does not contribute to the path, thus nothing is returned.
        return '';
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    RouteInterface::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}
