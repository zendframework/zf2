<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

trait ServiceTrait
{

    /**
     * Route plugin manager
     *
     * @var ListenerInterface
     */
    protected $routePluginManager;

    /**
     * Set the route plugin manager.
     *
     * @param  ListenerInterface $rm
     * @return self
     */
    public function setRoutePluginManager(ListenerInterface $rm)
    {
        $this->routePluginManager = $rm;
        return $this;
    }

    /**
     * Get the route plugin manager.
     *
     * @return ListenerInterface
     */
    public function getRoutePluginManager()
    {
        return $this->routePluginManager;
    }
}
