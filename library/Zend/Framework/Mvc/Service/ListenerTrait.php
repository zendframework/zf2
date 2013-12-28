<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\Service;

use Exception;
use Zend\Framework\Application\ServiceTrait as Services;
use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\Service\Event as ServiceEvent;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService, Services;

    /**
     * @var ListenerConfig
     */
    protected $config;

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
     * @return object
     */
    public function getService($name)
    {
        $event = new Event($name);

        $event->setServiceManager($this->sm)
              ->setEventManager($this->em);

        return $this->get($event);
    }

    /**
     * @param EventInterface $event
     * @return bool|object
     */
    public function get(EventInterface $event)
    {
        return $this->__invoke($event);
    }

    /**
     * @param EventInterface $event
     * @return bool|object
     * @throws Exception
     */
    public function __invoke(EventInterface $event)
    {
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $name = $event->service();

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

            $service = new ServiceEvent($name, $event->options());

            $service->setServiceManager($sm);

            $em->__invoke($service);

            $this->listeners[$name] = $service->listener();

            $instance = $service->instance();

            if ($event->shared()) {
                $this->shared[$name] = $instance;
            }
        }

        $this->pending[$name] = false;

        return $instance;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->config->get($name);
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
}
