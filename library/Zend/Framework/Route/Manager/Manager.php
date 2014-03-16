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
use Zend\Stdlib\RequestInterface as Request;
use Zend\Framework\Route\Assemble\AssembleInterface;
use Zend\Framework\Route\Assemble\AssemblerInterface;
use Zend\Framework\Route\Assemble\ServiceTrait as RouteAssembler;
use Zend\Framework\Route\Config\ConfigInterface as RouteConfigInterface;
use Zend\Framework\Route\EventInterface as Event;
use Zend\Framework\Service\AliasTrait as Alias;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\RouteMatch;

class Manager
    implements
        AssembleInterface,
        AssemblerInterface,
        EventManagerInterface,
        ManagerInterface,
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
        return $this->url($params, $options);
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

        $config = $this->routes->routes()->get($listener);

        $route = $this->create($this->alias($config['type']), $config);

        if (empty($config['child_routes'])) {
            return $route;
        }

        $options = array(
            'manager'       => $this,
            'route'         => $route,
            'may_terminate' => !empty($options['may_terminate']),
            'child_routes'  => $config['child_routes']
        );

        return $this->create($this->alias('part'), $options);
    }

    /**
     * @param Request $request
     * @param null $pathOffset
     * @param null $options
     * @return null|RouteMatch
     */
    public function match(Request $request, $pathOffset = null, $options = null)
    {
        return $this->trigger([Event::EVENT, $request, $pathOffset], $options);
    }

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function route($name, $options = null)
    {
        $route = $this->create($this->alias($name), $options);

        if (empty($options['child_routes'])) {
            return $route;
        }

        $options = array(
            'manager'       => $this,
            'route'         => $route,
            'may_terminate' => !empty($options['may_terminate']),
            'child_routes'  => $options['child_routes']
        );

        return $this->create($this->alias('part'), $options);
    }

    /**
     * @return RoutesConfigInterface
     */
    public function routes()
    {
        return $this->routes;
    }
}
