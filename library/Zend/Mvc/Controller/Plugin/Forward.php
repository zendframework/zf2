<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\EventManager\SharedEventManagerInterface as SharedEvents;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class Forward extends AbstractPlugin
{
    /**
     * @var ControllerManager
     */
    protected $controllers;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var int
     */
    protected $maxNestedForwards = 10;

    /**
     * @var int
     */
    protected $numNestedForwards = 0;

    /**
     * @var array
     */
    protected $listenersToDetach = null;

    /**
     * @param ControllerManager $controllers
     */
    public function __construct(ControllerManager $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * Set maximum number of nested forwards allowed
     *
     * @param  int $maxNestedForwards
     * @return Forward
     */
    public function setMaxNestedForwards($maxNestedForwards)
    {
        $this->maxNestedForwards = (int) $maxNestedForwards;
        return $this;
    }

    /**
     * Get information on listeners that need to be detached before dispatching.
     *
     * Each entry in the array contains three keys:
     *
     * id (identifier for event-emitting component),
     * event (the hooked event)
     * and class (the class of listener that should be detached).
     *
     * @return array
     */
    public function getListenersToDetach()
    {
        // If a blacklist has not been explicitly set, return the default:
        if (null === $this->listenersToDetach) {
            // We need to detach the InjectViewModelListener to prevent templates
            // from getting attached to the ViewModel twice when a calling action
            // returns the output generated by a forwarded action.
            $this->listenersToDetach = array(array(
                'id'    => 'Zend\Stdlib\DispatchableInterface',
                'event' => MvcEvent::EVENT_DISPATCH,
                'class' => 'Zend\Mvc\View\Http\InjectViewModelListener',
            ));
        }
        return $this->listenersToDetach;
    }

    /**
     * Set information on listeners that need to be detached before dispatching.
     *
     * @param  array $listeners Listener information; see getListenersToDetach() for details on format.
     * @return void
     */
    public function setListenersToDetach($listeners)
    {
        $this->listenersToDetach = $listeners;
    }

    /**
     * Dispatch another controller
     *
     * @param  string $name Controller name; either a class name or an alias used in the controller manager
     * @param  null|array $params Parameters with which to seed a custom RouteMatch object for the new controller
     * @return mixed
     * @throws Exception\DomainException if composed controller does not define InjectApplicationEventInterface
     *         or Locator aware; or if the discovered controller is not dispatchable
     */
    public function dispatch($name, array $params = null)
    {
        $event   = clone($this->getEvent());

        $controller = $this->controllers->get($name);
        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($event);
        }

        // Allow passing parameters to seed the RouteMatch with & copy matched route name
        if ($params !== null) {
            $routeMatch = new RouteMatch($params);
            $routeMatch->setMatchedRouteName($event->getRouteMatch()->getMatchedRouteName());
            $event->setRouteMatch($routeMatch);
        }

        if ($this->numNestedForwards > $this->maxNestedForwards) {
            throw new Exception\DomainException("Circular forwarding detected: greater than $this->maxNestedForwards nested forwards");
        }
        $this->numNestedForwards++;

        // Detach listeners that may cause problems during dispatch:
        $sharedEvents = $event->getApplication()->getEventManager()->getSharedManager();
        $listeners = $this->detachProblemListeners($sharedEvents);

        $return = $controller->dispatch($event->getRequest(), $event->getResponse());

        // If we detached any listeners, reattach them now:
        $this->reattachProblemListeners($sharedEvents, $listeners);

        $this->numNestedForwards--;

        return $return;
    }

    /**
     * Detach problem listeners specified by getListenersToDetach() and return an array of information that will
     * allow them to be reattached.
     *
     * @param  SharedEvents $sharedEvents Shared event manager
     * @return array
     */
    protected function detachProblemListeners(SharedEvents $sharedEvents)
    {
        // Convert the problem list from two-dimensional array to more convenient id => event => class format:
        $formattedProblems = array();
        foreach ($this->getListenersToDetach() as $current) {
            if (!isset($formattedProblems[$current['id']])) {
                $formattedProblems[$current['id']] = array();
            }
            if (!isset($formattedProblems[$current['id']][$current['event']])) {
                $formattedProblems[$current['id']][$current['event']] = array();
            }
            $formattedProblems[$current['id']][$current['event']][] = $current['class'];
        }

        // Loop through the class blacklist, detaching problem events and remembering their CallbackHandlers
        // for future reference:
        $results = array();
        foreach ($formattedProblems as $id => $eventArray) {
            $results[$id] = array();
            foreach ($eventArray as $eventName => $classArray) {
                $results[$id][$eventName] = array();
                $events = $sharedEvents->getListeners($id, $eventName);
                foreach ($events as $currentEvent) {
                    $currentCallback = $currentEvent->getCallback();
                    if (!isset($currentCallback[0])) {
                        continue;
                    }
                    foreach ($classArray as $class) {
                        if (is_a($currentCallback[0], $class)) {
                            $sharedEvents->detach($id, $currentEvent);
                            $results[$id][$eventName][] = $currentEvent;
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Reattach all problem listeners detached by detachProblemListeners(), if any.
     *
     * @param  SharedEvents $sharedEvents Shared event manager
     * @param  array        $listeners    Output of detachProblemListeners()
     * @return void
     */
    protected function reattachProblemListeners(SharedEvents $sharedEvents, array $listeners)
    {
        foreach ($listeners as $id => $eventArray) {
            foreach ($eventArray as $eventName => $callbacks) {
                foreach ($callbacks as $current) {
                    $sharedEvents->attach($id, $eventName, $current->getCallback(), $current->getMetadatum('priority'));
                }
            }
        }
    }

    /**
     * Get the event
     *
     * @return MvcEvent
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Forward plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = array();
            if ($event) {
                $params = $event->getParams();
            }
            $event  = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }
}
