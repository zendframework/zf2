<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Controller\Error\EventInterface as Error;
use Zend\Framework\Controller\EventInterface;
use Zend\Framework\Controller\Exception\EventInterface as Exception;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

class Manager
    implements EventManagerInterface, ManagerInterface, ServiceManagerInterface
{
    /**
     *
     */
    use EventGenerator,
        EventManager,
        Factory,
        ServiceManager;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config    = $config;
        $this->listeners = $config->controllers();
        $this->services  = $config->services();
    }

    /**
     * @param null|RouteMatch $routeMatch
     * @param null|string $controller
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function error(Request $request, Response $response, RouteMatch $routeMatch = null, $controller = null)
    {
        return $this->trigger([Error::EVENT, $routeMatch, $controller], [$request, $response]);
    }

    /**
     * Retrieve event from service manager
     *
     * @param array|EventInterface|string $event
     * @return EventInterface
     */
    protected function event($event)
    {
        return $event instanceof EventInterface ? $event : $this->create($event);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception $exception
     * @return mixed
     */
    public function exception(Request $request, Response $response, \Exception $exception)
    {
        return $this->trigger([Exception::EVENT, $exception], [$request, $response]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param RouteMatch $routeMatch
     * @param null $controller
     * @return mixed
     */
    public function dispatch(Request $request, Response $response, RouteMatch $routeMatch = null, $controller = null)
    {
        return $this->trigger([$controller, $routeMatch, $controller], [$request, $response]);
    }

    /**
     * @param string $controller
     * @return bool
     */
    public function dispatchable($controller)
    {
        return $this->listeners()->has($controller);
    }

    /**
     * Retrieve listener from service manager
     *
     * @param array|callable|string $listener
     * @return callable
     */
    protected function listener($listener)
    {
        return is_callable($listener) ? $listener : $this->create($listener);
    }
}
