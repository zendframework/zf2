<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface;
use Zend\Mvc\Router\RouteMatch;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT = 'Application\Event';

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * @param ListenerInterface $listener
     * @param $options
     * @return mixed
     */
    public function __invoke(ListenerInterface $listener, $options = null);
}
