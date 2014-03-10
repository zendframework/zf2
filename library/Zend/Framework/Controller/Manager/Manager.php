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
     * @param $routeMatch
     * @param $controller
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error($routeMatch, $controller, $request, $response)
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
     * @param $exception
     * @param $request
     * @param $response
     * @return mixed
     */
    public function exception($exception, $request, $response)
    {
        return $this->trigger([Exception::EVENT, $exception], [$request, $response]);
    }

    /**
     * @param $controller
     * @param $routeMatch
     * @param $request
     * @param $response
     * @return mixed
     */
    public function dispatch($controller, $routeMatch, $request, $response)
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
