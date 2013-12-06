<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module;

use Zend\Framework\EventManager\EventManagerInterface as EventManager;
use Zend\Framework\EventManager\Event;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Mvc\Router\Http\RouteMatch;

class RouteListener implements ListenerAggregateInterface
{
    const MODULE_NAMESPACE    = '__NAMESPACE__';
    const ORIGINAL_CONTROLLER = '__CONTROLLER__';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManager $events
     * @param  int $priority
     */
    public function attach(EventManager $em, $priority = 1)
    {
        $this->listeners[] = $em->attach(new CallbackListener(array($this, 'onRoute'), MvcEvent::EVENT_ROUTE, null, $priority));
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManager $em)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($em->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "route" event and determine if the module namespace should
     * be prepended to the controller name.
     *
     * If the route match contains a parameter key matching the MODULE_NAMESPACE
     * constant, that value will be prepended, with a namespace separator, to
     * the matched controller parameter.
     *
     * @param  Event $e
     * @return null
     */
    public function onRoute(Event $e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches instanceof RouteMatch) {
            // Can't do anything without a route match
            return;
        }

        $module = $matches->getParam(self::MODULE_NAMESPACE, false);
        if (!$module) {
            // No module namespace found; nothing to do
            return;
        }

        $controller = $matches->getParam('controller', false);
        if (!$controller) {
            // no controller matched, nothing to do
            return;
        }

        // Ensure the module namespace has not already been applied
        if (0 === strpos($controller, $module)) {
            return;
        }

        // Keep the originally matched controller name around
        $matches->setParam(self::ORIGINAL_CONTROLLER, $controller);

        // Prepend the controllername with the module, and replace it in the
        // matches
        $controller = $module . '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
        $matches->setParam('controller', $controller);
    }

    public function __invoke(ServiceManager $sm)
    {
        return $this;
    }
}
