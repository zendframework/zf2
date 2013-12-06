<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc;

use Zend\Framework\EventManager\EventManager;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\MvcEvent;

class RouteListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManager $events)
    {
        $this->listeners[] = $events->attach(new CallbackListener(array($this, 'onRoute'), MvcEvent::EVENT_ROUTE));
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManager $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "route" event and attempt to route the request
     *
     * If no matches are returned, triggers "dispatch.error" in order to
     * create a 404 response.
     *
     * Seeds the event with the route match on completion.
     *
     * @param  MvcEvent $e
     * @return null|Router\RouteMatch
     */
    public function onRoute($e)
    {
        $target     = $e->getApplication(); //$e->getTarget();
        $request    = $e->getRequest();
        $router     = $e->getRouter();
        $routeMatch = $router->match($request);

        if ($routeMatch instanceof Router\RouteMatch) {
            $e->setRouteMatch($routeMatch);
        }

        return $routeMatch;
    }

    public function __invoke(ServiceManager $sm)
    {
        return $this;
    }
}
