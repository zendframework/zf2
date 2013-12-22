<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Exception;
use Zend\Framework\ServiceManager\ConfigInterface as Config;

class ServiceManager implements ServiceLocatorInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

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
        return $this->get(new ServiceRequest($name));
    }

    /**
     * @param ServiceRequest $service
     * @return object
     * @throws Exception
     */
    public function get(ServiceRequest $service)
    {
        $name = $service->getName();

        if ($service->isShared() && isset($this->shared[$name])) {
            return $this->shared[$name];
        }

        if (!empty($this->pending[$name])) {
            throw new Exception('Circular dependency: '.$name);
        }

        $this->pending[$name] = true;

        //allow service event access to the SM
        $service->setTarget($this);

        if (isset($this->listeners[$name])) {

            $instance = $this->listeners[$name]($service);

        } else {

            foreach($service->getListeners() as $listener) {
                $instance = $service($listener);
                if (!$instance) {
                    continue;
                }

                if ($service->isShared()) {
                    $this->shared[$name] = $instance;
                    break;
                }

                $this->listeners[$name] = $listener;
                break;
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
     * @return $this
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
