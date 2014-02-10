<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ConfigInterface as EventConfig;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\ConfigInterface as ServiceConfig;
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
        ServiceManager;

    /**
     * @param ServiceConfig $services
     * @param EventConfig $listeners
     */
    public function __construct(ServiceConfig $services, EventConfig $listeners)
    {
        $this->services  = $services;
        $this->listeners = $listeners;
    }
    /**
     * Retrieve event from service manager
     *
     * @param array|EventInterface|string $event
     * @return EventInterface
     */
    protected function event($event)
    {
        return $event instanceof EventInterface ? $event : $this->get($event);
    }

    /**
     * Retrieve listener from service manager
     *
     * @param array|ListenerInterface|string $listener
     * @return ListenerInterface
     */
    protected function listener($listener)
    {
        return $listener instanceof ListenerInterface ? $listener : $this->get($listener);
    }

    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function run($event = Event::EVENT, $options = null)
    {
        return $this->trigger($event, $options);
    }
}
