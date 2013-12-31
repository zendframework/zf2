<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\ListenerConfigInterface;
use Zend\Framework\Service\ListenerTrait as Listener;
use Zend\Framework\Service\Event as Plugin;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * @param $name
     * @return string
     */
    public function alias($name)
    {
        return $this->config->get(strtolower($name)) ? : $name;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->config($this->alias($name));
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
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->sm->addInvokableClass($this->alias($name), $class);
    }

    /**
     * @param string $name
     * @param array $options
     * @return object
     */
    public function get($name, array $options = [])
    {
        return $this->sm->__invoke(new Plugin($this->alias($name), $options));
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        return $this->sm->add($this->alias($name), $service);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($this->alias($name));
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        return $this->sm->__invoke($event->setService($this->alias($event->name())));
    }
}
