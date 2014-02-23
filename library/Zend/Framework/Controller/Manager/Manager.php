<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Controller\EventInterface;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Controller\ConfigInterface as ControllerConfig;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\ConfigInterface as ServiceConfig;
use Zend\Framework\Service\Factory\ServiceTrait as ServiceFactory;
use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\ManagerTrait as ServiceManager;

class Manager
    implements EventManagerInterface, ManagerInterface, ServiceManagerInterface
{
    /**
     *
     */
    use EventGenerator,
        EventManager,
        ServiceFactory,
        ServiceManager;

    /**
     * @param ServiceConfig $services
     * @param ControllerConfig $controllers
     */
    public function __construct(ServiceConfig $services, ControllerConfig $controllers)
    {
        $this->services  = $services;
        $this->listeners = $controllers;
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
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function dispatch($event, $options = null)
    {
        return $this->trigger($event, $options);
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
