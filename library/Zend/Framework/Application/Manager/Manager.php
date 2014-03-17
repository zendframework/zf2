<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Manager;

use Zend\Framework\Application\EventInterface as Application;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;

class Manager
    implements EventManagerInterface, ManagerInterface
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
        $this->listeners = $config->listeners();
        $this->services  = $config->services();
    }

    /**
     * @param array|Application|string $event
     * @return Application
     */
    protected function event($event)
    {
        return $event instanceof Application ? $event : $this->create($event);
    }

    /**
     * @param array|callable|string $listener
     * @param null $options
     * @return callable
     */
    protected function listener($listener, $options = null)
    {
        return is_callable($listener) ? $listener : $this->create($listener, $options);
    }

    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function run($event = Application::EVENT, $options = null)
    {
        return $this->trigger($event, $options);
    }
}
