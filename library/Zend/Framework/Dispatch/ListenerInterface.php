<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Event\Manager\ManagerInterface as EventManager;
use Zend\Framework\Service\ManagerInterface as ServiceManager;
use Zend\Mvc\Router\RouteMatch;

interface ListenerInterface
    extends Listener
{
    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @param EventInterface $event
     * @param RouteMatch $routeMatch
     * @return mixed
     */
    public function __invoke(EventInterface $event, RouteMatch $routeMatch);
}
