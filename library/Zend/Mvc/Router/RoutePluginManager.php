<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Router;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\Config as Config;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for routes
 *
 * Enforces that routes retrieved are instances of RouteInterface. It overrides
 * createFromInvokable() to call the route's factory method in order to get an
 * instance. The manager is marked to not share by default, in order to allow
 * multiple route instances of the same type.
 */
class RoutePluginManager
    extends AbstractPluginManager
{

    /**
     * @param ServiceManager $sm
     * @return mixed|AbstractPluginManager
     */
    public function createService(ServiceManager $sm)
    {
        $service = new static();

        $service->setServiceManager($sm)
                ->setConfig(new Config($sm->getApplicationConfig()['router']));

        return $service;
    }

    /**
     * @param $name
     * @param $options
     * @return mixed
     */
    public function get($name, $options)
    {
        return $this->sm->get(new ServiceRequest($name, $options));
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->sm->addInvokableClass($name, $class);
    }
}
