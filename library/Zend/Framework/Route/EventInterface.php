<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Http\PhpEnvironment\Request as Request;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT_ROUTE = 'mvc.route';

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request);

    /**
     * @param RouteMatch $routeMatch
     * @return $this
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router);

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceManager(ServiceManager $sm);
}
