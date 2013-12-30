<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Exception;
use ReflectionClass;
use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Framework\Service\Factory\Listener as FactoryService;

trait ListenerTrait
{
    /**
     *
     */
    use Listener, ServicesTrait;

    /**
     * @var ListenerConfig
     */
    public $config;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->config->add($name, $class);
    }

    /**
     * @param $name
     * @return string
     */
    public function alias($name)
    {
        return $name;
    }

    /**
     * @param $name
     * @return object
     */
    public function get($name)
    {
        return $this->__invoke(new Event($name));
    }

    /**
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->config->get($name);
    }

    /**
     * @param ListenerConfigInterface $config
     * @return $this
     */
    public function setConfig(ListenerConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->shared[$name] = $service;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->shared[$name]);
    }

    /**
     * @param EventInterface $event
     * @return bool|object
     * @throws Exception
     */
    public function __invoke(EventInterface $event)
    {
        $name = $event->alias();

        if ($event->shared() && isset($this->shared[$name])) {
            return $this->shared[$name];
        }

        if (!empty($this->pending[$name])) {
            throw new Exception('Circular dependency: '.$name);
        }

        $this->pending[$name] = true;

        if (isset($this->listeners[$name])) {

            $instance = $this->listeners[$name]->__invoke($event);

        } else {

            $listener = new FactoryService($this);

            $instance = false;

            $factory = $this->config($name);

            if ($factory) {
                $instance = $listener->__invoke($factory, $event->options());
            }

            $this->listeners[$name] = $listener;

            if ($event->shared()) {
                $this->shared[$name] = $instance;
            }
        }

        $this->pending[$name] = false;

        return $instance;
    }
}
