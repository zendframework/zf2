<?php

namespace Zend\Navigation;

use Zend\EventManager\Event,
    Zend\EventManager\StaticListenerAggregate,
    Zend\EventManager\StaticEventCollection;

class MvcInjectRouteMatchListener implements StaticListenerAggregate
{

    /**
     * Listeners we've registered
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach listeners
     *
     * @param  Events $events
     * @return void
     */
    public function attach($id, StaticEventCollection $events)
    {
        $this->listeners[$id][] = $events->attach($id, 'dispatch', array($this, 'injectRouteMatch'), 10);
    }

    /**
     * Detach listeners
     *
     * @param  Events $events
     * @return void
     */
    public function detach($id, StaticEventCollection $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$id][$index]);
            }
        }
    }

    /**
     * Injects the computed route match into the navigation locator.
     *
     * @param \Zend\EventManager\Event $e
     * @return void
     */
    public function injectRouteMatch(Event $e)
    {
        $controller = $e->getTarget();
        $locator    = $controller->getLocator();
        $service    = $locator->get('Zend\Navigation\NavigationLocator');
        $service->setRouteMatch($e->getRouteMatch());
    }
}