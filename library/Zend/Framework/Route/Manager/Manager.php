<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Event\Config\ConfigInterface as RoutesConfigInterface;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Route\Assemble\AssembleInterface;
use Zend\Framework\Route\Assemble\ServiceTrait as RouteAssembler;
use Zend\Framework\Route\Config\ConfigInterface as RouteConfigInterface;
use Zend\Framework\Route\EventInterface as Event;
use Zend\Framework\Route\Match\MatchInterface as RouteMatchInterface;
use Zend\Framework\Service\AliasTrait as Alias;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

class Manager
    implements
        AssembleInterface,
        EventManagerInterface,
        ManagerInterface,
        RouteMatchInterface,
        ServiceManagerInterface
{
    /**
     *
     */
    use Alias,
        EventGenerator,
        EventManager,
        Factory,
        RouteAssembler,
        ServiceManager;

    /**
     * @var RouteConfigInterface
     */
    protected $routes;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $routes = $config->routes();

        $this->alias     = $routes->aliases();
        $this->config    = $config;
        $this->listeners = $routes->listeners();
        $this->routes    = $routes;
        $this->services  = $config->services();
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function assemble(array $params = [], array $options = [])
    {
        if (!isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $name = explode('/', $options['name'], 2);

        list($name, $children) = [$name[0], isset($name[1]) ? $name[1] : null];

        $route = $this->routes->routes()->get($name);

        $route = $this->route($route['type'], $route['options']);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $name));
        }

        if ($children) {

            if (!$route instanceof Manager) {
                throw new Exception\RuntimeException(sprintf('Route with name "%s" does not have child routes', $name));
            }

            $options['name'] = $children;

        } else {

            unset($options['name']);

        }

        return $this->build($route, $name, $params, $options);
    }

    /**
     * @param array|Event|string $event
     * @return Event
     */
    protected function event($event)
    {
        return $event instanceof Event ? $event : $this->create($event);
    }

    /**
     * @param array|callable|string $listener
     * @return callable
     */
    protected function listener($listener)
    {
        if (is_callable($listener)) {
            return $listener;
        }

        $listener = $this->routes->routes()->get($listener);

        return $this->route($listener['type'], $listener['options']);
    }

    /**
     * @param Request $request
     * @param null $baseUrlLength
     * @param null $pathOffset
     * @param null $options
     * @return null|RouteMatch
     */
    public function match(Request $request, $baseUrlLength = null, $pathOffset = null, $options = null)
    {
        return $this->trigger([Event::EVENT, $request, $baseUrlLength, $pathOffset], $options);
    }

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function route($name, $options = null)
    {
        return $this->create($this->alias($name), $options);
    }

    /**
     * @return RoutesConfigInterface
     */
    public function routes()
    {
        $this->routes;
    }
}
