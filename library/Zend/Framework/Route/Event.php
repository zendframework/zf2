<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Event as EventManagerEvent;
use Zend\Stdlib\ResponseInterface as Response;

class Event extends EventManagerEvent
{
    protected $name = MvcEvent::EVENT_ROUTE;

    protected $request;

    protected $router;

    protected $error;

    protected $routeMatch;

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    public function setRouteMatch($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * @return callable
     */
    public function getDefaultCallback()
    {
        return function($event, $listener, $response) {
            if ($response instanceof Response || $event->getError()) {
                $event->stopPropagation();
            }
        };
    }
}
