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
use Zend\Framework\Event\ListenerTrait as Listener;
use Zend\Framework\Service\Factory\CallableListener as CallableFactory;
use Zend\Framework\Service\Factory\InstanceListener as InstanceFactory;
use Zend\Framework\Service\Factory\ListenerInterface as Factory;

trait ListenerTrait
{
    /**
     *
     */
    use Listener,
        ServiceTrait;

    /**
     * @var array
     */
    public $listeners = [];

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $pending = [];

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
     * @param string $class
     */
    public function configure($name, $class)
    {
        $this->listeners[$name] = $class;
    }

    /**
     * @param string|callable $factory
     * @return Factory
     */
    public function factory($factory)
    {
        if (is_string($factory)) {
            if (is_subclass_of($factory, Factory::class)) {
                return new $factory($this->sm);
            }

            if (is_callable($factory)) {
                return new CallableFactory($this->sm, $factory);
            }

            return new InstanceFactory($this->sm, $factory);
        }

        if (is_callable($factory)) {
            return new CallableFactory($this->sm, $factory);
        }

        return $factory;
    }

    /**
     * @param $name
     * @param array $options
     * @return bool|object
     */
    public function get($name, array $options = [])
    {
        return $this->__invoke(new Event($name, $options));
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
     * @param $name
     * @return bool|Factory|callable
     */
    public function listener($name)
    {
        if (empty($this->listeners[$name])) {
            return false;
        }

        return $this->listeners[$name] = $this->factory($this->listeners[$name]);
    }

    /**
     * @param array $listeners
     * @return self
     */
    public function listeners(array $listeners)
    {
        $this->listeners = $listeners;
        return $this;
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

        $instance = false;

        $listener = $this->listener($name);

        if ($listener) {
            $instance = $event->__invoke($listener);
        }

        if ($event->shared()) {
            $this->shared[$name] = $instance;
        }

        $this->pending[$name] = false;

        return $instance;
    }
}
