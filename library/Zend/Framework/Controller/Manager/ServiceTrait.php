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

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $cm;

    /**
     * @param RouteMatch $routeMatch
     * @return false|ListenerInterface
     */
    public function controller(RouteMatch $routeMatch)
    {
        return $this->cm->controller($routeMatch);
    }

    /**
     * @return ManagerInterface
     */
    public function controllerManager()
    {
        return $this->cm;
    }

    /**
     * @param ManagerInterface $cm
     * @return self
     */
    public function setControllerManager(ManagerInterface $cm)
    {
        $this->cm = $cm;
        return $this;
    }
}
