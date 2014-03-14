<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Route\Config\ConfigInterface as RoutesConfigInterface;
use Zend\Framework\Route\EventInterface as Event;
use Zend\Framework\Service\AliasTrait as Alias;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;
use Zend\Stdlib\RequestInterface as Request;


class Manager
    implements ManagerInterface, EventManagerInterface, ServiceManagerInterface
{
    /**
     *
     */
    use Alias,
        EventGenerator,
        EventManager,
        Factory,
        ServiceManager;

    /**
     * @var RoutesConfigInterface
     */
    protected $router;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config    = $config;
        $this->router    = $config->router();

        $this->alias     = $this->router->plugins();
        $this->listeners = $config->listeners();
        $this->services  = $config->services();
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
     * Retrieve listener from service manager
     *
     * @param array|callable|string $listener
     * @return callable
     */
    protected function listener($listener)
    {
        return is_callable($listener) ? $listener : $this->create($listener);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function route(Request $request)
    {
        return $this->trigger(Event::EVENT, $request);
    }

    /**
     * @return array|RoutesConfigInterface
     */
    public function routes()
    {
        return $this->router;
    }
}
