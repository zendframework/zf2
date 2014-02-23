<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Serializable;
use Zend\Framework\Application\Config\ConfigInterface as Config;
use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\Factory\ServiceTrait as ServiceFactory;
use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\ManagerTrait as ServiceManager;

class Manager
    implements EventManagerInterface, ManagerInterface, Serializable, ServiceManagerInterface
{
    /**
     *
     */
    use EventGenerator,
        EventManager,
        ServiceFactory,
        ServiceManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config    = $config;
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
        return $event instanceof EventInterface ? $event : $this->get($event, null, false);
    }

    /**
     * Retrieve listener from service manager
     *
     * @param array|callable|string $listener
     * @return callable
     */
    protected function listener($listener)
    {
        return is_callable($listener) ? $listener : $this->get($listener, null, false);
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

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->config);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->__construct(unserialize($serialized));

        $this->services->add('Config', $this->config)
                       ->add('EventManager', $this);
    }
}
