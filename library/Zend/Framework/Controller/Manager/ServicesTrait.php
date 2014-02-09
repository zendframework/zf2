<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Event\ListenerInterface;
use Zend\Mvc\Router\RouteMatch;

trait ServicesTrait
{
    /**
     * @param RouteMatch $routeMatch
     * @return false|ListenerInterface
     */
    public function controller(RouteMatch $routeMatch)
    {
        return $this->controllerManager()->controller($routeMatch);
    }

    /**
     * @return bool|ManagerInterface
     */
    public function controllerManager()
    {
        return $this->sm->get('Controller\Manager');
    }

    /**
     * @param ManagerInterface $cm
     * @return self
     */
    public function setControllerManager(ManagerInterface $cm)
    {
        return $this->sm->add('Controller\Manager', $cm);
    }
}
