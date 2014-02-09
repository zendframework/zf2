<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Mvc\Router\RouteMatch as RouteMatch;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';
    const ERROR_EXCEPTION                  = 'error-exception';

    /**
     *
     */
    const EVENT_NAME = 'Route\Event';

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * @param ListenerInterface $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(ListenerInterface $listener, $options = null);
}
