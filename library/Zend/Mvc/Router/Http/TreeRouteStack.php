<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Router\Http;

use ArrayObject;
use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Uri\Http as HttpUri;

/**
 * Tree search implementation.
 */
class TreeRouteStack extends SimpleRouteStack
{
    /**
     * Base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Request URI.
     *
     * @var HttpUri
     */
    protected $requestUri;

    /**
     * Prototype routes.
     *
     * We use an ArrayObject in this case so we can easily pass it down the tree
     * by reference.
     *
     * @var ArrayObject
     */
    protected $prototypes;

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::factory()
     * @param  array|Traversable $options
     * @return SimpleRouteStack
     * @throws Exception\InvalidArgumentException
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        $instance = parent::factory($options);

        if (isset($options['prototypes'])) {
            $instance->addPrototypes($options['prototypes']);
        }

        return $instance;
    }

    /**
     * init(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::init()
     */
    protected function init()
    {
        $this->prototypes = new ArrayObject;

        $routes = $this->routePluginManager;
        foreach (array(
                'hostname' => __NAMESPACE__ . '\Hostname',
                'literal'  => __NAMESPACE__ . '\Literal',
                'part'     => __NAMESPACE__ . '\Part',
                'chain'    => __NAMESPACE__ . '\Chain',
                'regex'    => __NAMESPACE__ . '\Regex',
                'scheme'   => __NAMESPACE__ . '\Scheme',
                'segment'  => __NAMESPACE__ . '\Segment',
                'wildcard' => __NAMESPACE__ . '\Wildcard',
                'query'    => __NAMESPACE__ . '\Query',
                'method'   => __NAMESPACE__ . '\Method',
            ) as $name => $class
        ) {
            $routes->setInvokableClass($name, $class);
        };
    }

    /**
     * addRoute(): defined by RouteStackInterface interface.
     *
     * @see    RouteStackInterface::addRoute()
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return TreeRouteStack
     */
    public function addRoute($name, $route, $priority = null)
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        return parent::addRoute($name, $route, $priority);
    }

    /**
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  string|array|Traversable $specs
     * @return RouteInterface
     * @throws Exception\InvalidArgumentException When route definition is not an array nor traversable
     * @throws Exception\InvalidArgumentException When chain routes are not an array nor traversable
     * @throws Exception\RuntimeException         When a generated routes does not implement the HTTP route interface
     */
    protected function routeFromArray($specs)
    {
        if (is_string($specs)) {
            if (null === ($route = $this->getPrototype($specs))) {
                throw new Exception\RuntimeException(sprintf('Could not find prototype with name %s', $specs));
            }

            return $route;
        } elseif ($specs instanceof Traversable) {
            $specs = ArrayUtils::iteratorToArray($specs);
        } elseif (!is_array($specs)) {
            throw new Exception\InvalidArgumentException('Route definition must be an array or Traversable object');
        }

        if (isset($specs['chain_routes'])) {
            if (!is_array($specs['chain_routes'])) {
                throw new Exception\InvalidArgumentException('Chain routes must be an array or Traversable object');
            }

            $chainRoutes = array_merge(array($specs), $specs['chain_routes']);
            unset($chainRoutes[0]['chain_routes']);

            $options = array(
                'routes'        => $chainRoutes,
                'route_plugins' => $this->routePluginManager,
                'prototypes'    => $this->prototypes,
            );

            $route = $this->routePluginManager->get('chain', $options);
        } else {
            $route = parent::routeFromArray($specs);
        }

        if (!$route instanceof RouteInterface) {
            throw new Exception\RuntimeException('Given route does not implement HTTP route interface');
        }

        if (isset($specs['child_routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['child_routes'],
                'route_plugins' => $this->routePluginManager,
                'prototypes'    => $this->prototypes,
            );

            $priority = (isset($route->priority) ? $route->priority : null);

            $route = $this->routePluginManager->get('part', $options);
            $route->priority = $priority;
        }

        return $route;
    }

    /**
     * Add multiple prototypes at once.
     *
     * @param  Traversable $routes
     * @return TreeRouteStack
     * @throws Exception\InvalidArgumentException
     */
    public function addPrototypes($routes)
    {
        if (!is_array($routes) && !$routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addPrototypes expects an array or Traversable set of routes');
        }

        foreach ($routes as $name => $route) {
            $this->addPrototype($name, $route);
        }

        return $this;
    }

    /**
     * Add a prototype.
     *
     * @param  string $name
     * @param  mixed  $route
     * @return TreeRouteStack
     */
    public function addPrototype($name, $route)
    {
        if (!$route instanceof RouteInterface) {
            $route = $this->routeFromArray($route);
        }

        $this->prototypes[$name] = $route;

        return $this;
    }

    /**
     * Get a prototype.
     *
     * @param  string $name
     * @return RouterInterface|null
     */
    public function getPrototype($name)
    {
        if (isset($this->prototypes[$name])) {
            return $this->prototypes[$name];
        }

        return null;
    }

    /**
     * match(): defined by \Zend\Mvc\Router\RouteInterface
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

        if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
            $this->setBaseUrl($request->getBaseUrl());
        }

        $uri           = $request->getUri();
        $baseUrlLength = strlen($this->baseUrl) ?: null;

        if ($this->requestUri === null) {
            $this->setRequestUri($uri);
        }

        if ($baseUrlLength !== null) {
            $pathLength = strlen($uri->getPath()) - $baseUrlLength;

            foreach ($this->routes as $name => $route) {
                if (($match = $route->match($request, $baseUrlLength)) instanceof RouteMatch && $match->getLength() === $pathLength) {
                    $match->setMatchedRouteName($name);

                    foreach ($this->defaultParams as $paramName => $value) {
                        if ($match->getParam($paramName) === null) {
                            $match->setParam($paramName, $value);
                        }
                    }

                    return $match;
                }
            }
        } else {
            return parent::match($request);
        }

        return null;
    }

    /**
     * assemble(): defined by \Zend\Mvc\Router\RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
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

        $names = explode('/', $options['name'], 2);
        $route = $this->routes->get($names[0]);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $names[0]));
        }

        if (isset($names[1])) {
            $options['name'] = $names[1];
        } else {
            unset($options['name']);
        }

        if (isset($options['only_return_path']) && $options['only_return_path']) {
            return $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);
        }

        if (!isset($options['uri'])) {
            $uri = new HttpUri();

            if (isset($options['force_canonical']) && $options['force_canonical']) {
                if ($this->requestUri === null) {
                    throw new Exception\RuntimeException('Request URI has not been set');
                }

                $uri->setScheme($this->requestUri->getScheme())
                    ->setHost($this->requestUri->getHost())
                    ->setPort($this->requestUri->getPort());
            }

            $options['uri'] = $uri;
        } else {
            $uri = $options['uri'];
        }

        $path = $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);

        if (isset($options['query'])) {
            $uri->setQuery($options['query']);
        }

        if (isset($options['fragment'])) {
            $uri->setFragment($options['fragment']);
        }

        if ((isset($options['force_canonical']) && $options['force_canonical']) || $uri->getHost() !== null) {
            if ($uri->getScheme() === null) {
                if ($this->requestUri === null) {
                    throw new Exception\RuntimeException('Request URI has not been set');
                }

                $uri->setScheme($this->requestUri->getScheme());
            }

            return $uri->setPath($path)->normalize()->toString();
        } elseif (!$uri->isAbsolute() && $uri->isValidRelative()) {
            return $uri->setPath($path)->normalize()->toString();
        }

        return $path;
    }

    /**
     * Set the base URL.
     *
     * @param  string $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the request URI.
     *
     * @param  HttpUri $uri
     * @return TreeRouteStack
     */
    public function setRequestUri(HttpUri $uri)
    {
        $this->requestUri = $uri;
        return $this;
    }

    /**
     * Get the request URI.
     *
     * @return HttpUri
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }
}
