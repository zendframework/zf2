<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Router;

use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

/**
 * Plugin manager implementation for routes
 *
 * Enforces that routes retrieved are instances of RouteInterface. It overrides
 * createFromInvokable() to call the route's factory method in order to get an
 * instance. The manager is marked to not share by default, in order to allow
 * multiple route instances of the same type.
 */
class RoutePluginManager
{
    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function setServiceLocator(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $name
     * @param $class
     */
    public function setInvokableClass($name, $class)
    {
        $this->sm->addInvokableClass($name, $class);
    }

    /**
     * @param ServiceRequest $name
     * @return mixed
     */
    public function get(ServiceRequest $service)
    {
        return $this->sm->get($service);
    }
}
