<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayObject;
use Traversable;

/**
 * Event manager: notification system
 *
 * Use the EventManager when you want to create a per-instance notification
 * system for your objects.
 */
class EventManager implements EventManagerInterface
{
    /**
     * Subscribed events and their listeners
     *
     * @var array
     */
    protected $events = array();

    /**
     * Identifiers, used to pull shared signals from SharedEventManagerInterface instance
     *
     * @var array
     */
    protected $identifiers = array();

    /**
     * Keep an array of hash => listeners
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Shared event manager
     *
     * @var null|SharedEventManagerInterface
     */
    protected $sharedManager = null;

    /**
     * Constructor
     *
     * Allows optionally specifying identifier(s) to use to pull signals from a
     * SharedEventManagerInterface.
     *
     * @param  null|string|int|array|Traversable $identifiers
     */
    public function __construct($identifiers = null)
    {
        $this->setIdentifiers($identifiers);
    }

    /**
     * Set shared event manager
     *
     * @param  SharedEventManagerInterface $sharedEventManager
     * @return void
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedManager = $sharedEventManager;
    }

    /**
     * Get shared event manager
     *
     * @return SharedEventManagerInterface|null
     */
    public function getSharedManager()
    {
        return $this->sharedManager;
    }

    /**
     * Attach a listener to an event
     *
     * The first argument is the event, and the next argument describes a
     * callback that will respond to that event.
     *
     * The last argument indicates a priority at which the event should be
     * executed. By default, this value is 1; however, you may set it for any
     * integer value. Higher values have higher priority (i.e., execute first).
     *
     * You can specify "*" for the event name. In such cases, the listener will
     * be triggered for every event.
     *
     * @param  string   $event An event or array of event names
     * @param  callable $listener
     * @param  int      $priority If provided, the priority at which to register the callable
     * @return callable if attaching callable (to allow later unsubscribe)
     * @throws Exception\InvalidArgumentException
     */
    public function attach($event, callable $listener, $priority = 1)
    {
        //$hash = spl_object_hash($listener);

        $this->events[(string) $event][(int) $priority][] = $listener;
        //$this->listeners[$hash]               = $listener;

        return $listener;
    }

    /**
     * Attach a listener aggregate
     *
     * Listener aggregates accept an EventManagerInterface instance, and call attach()
     * one or more times, typically to attach to multiple events using local
     * methods.
     *
     * @param  ListenerAggregateInterface $aggregate
     * @param  int                        $priority If provided, a suggested priority for the aggregate to use
     * @return mixed return value of {@link ListenerAggregateInterface::attach()}
     */
    public function attachAggregate(ListenerAggregateInterface $aggregate, $priority = 1)
    {
        return $aggregate->attach($this, $priority);
    }

    /**
     * Unsubscribe a listener from an event
     *
     * This method is quite inefficient as it needs to traverse each queue, so use with care!
     *
     * @param  callable $listener
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     */
    public function detach(callable $listener)
    {
        //$hash = spl_object_hash($listener);

        /*foreach ($this->events as &$event) {
            if (isset($event[$hash])) {
                unset($event[$hash]);
                unset($this->listeners[$hash]);

                return true;
            }
        }*/

        return false;
    }

    /**
     * Detach a listener aggregate
     *
     * Listener aggregates accept an EventManagerInterface instance, and call detach()
     * of all previously attached listeners.
     *
     * @param  ListenerAggregateInterface $aggregate
     * @return bool
     */
    public function detachAggregate(ListenerAggregateInterface $aggregate)
    {
        return $aggregate->detach($this);
    }

    /**
     * Trigger all listeners for a given event
     *
     * @param  string              $eventName
     * @param  EventInterface|null $event
     * @return ResponseCollection All listener return values
     */
    public function trigger($eventName, EventInterface $event = null)
    {
        // Initial value of stop propagation flag should be false
        $event = $event ?: new Event();
        $event->stopPropagation(false);

        return $this->triggerListeners($eventName, $event);
    }

    /**
     * Trigger listeners until return value of one causes a callback to
     * evaluate to true
     *
     * Triggers listeners until the provided callback evaluates the return
     * value of one as true, or until all listeners have been executed.
     *
     * @param  string              $eventName
     * @param  EventInterface|null $event
     * @param  callable|null       $callback
     * @return ResponseCollection
     */
    public function triggerUntil($eventName, EventInterface $event = null, callable $callback = null)
    {
        // Initial value of stop propagation flag should be false
        $event = $event ?: new Event();
        $event->stopPropagation(false);

        return $this->triggerListeners($eventName, $event, $callback);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvents()
    {
        return array_keys($this->events);
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners($eventName)
    {
        return isset($this->events[$eventName]) ? $this->events[$eventName] : array();
    }

    /**
     * {@inheritDoc}
     */
    public function clearListeners($eventName)
    {
        unset($this->events[$eventName]);
    }

    /**
     * {@inheritDoc}
     */
    public function setIdentifiers($identifiers)
    {
        if (is_array($identifiers) || $identifiers instanceof Traversable) {
            $this->identifiers = array_unique((array) $identifiers);
        } elseif ($identifiers !== null) {
            $this->identifiers = array($identifiers);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addIdentifiers($identifiers)
    {
        if (is_array($identifiers) || $identifiers instanceof Traversable) {
            $this->identifiers = array_unique(array_merge($this->identifiers, (array) $identifiers));
        } elseif ($identifiers !== null) {
            $this->identifiers = array_unique(array_merge($this->identifiers, array($identifiers)));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * Prepare arguments
     *
     * Use this method if you want to be able to modify arguments from within a
     * listener. It returns an ArrayObject of the arguments, which may then be
     * passed to trigger() or triggerUntil().
     *
     * @param  array $args
     * @return ArrayObject
     */
    public function prepareArgs(array $args)
    {
        return new ArrayObject($args);
    }

    /**
     * Trigger listeners
     *
     * Actual functionality for triggering listeners, to which both trigger() and triggerUntil()
     * delegate.
     *
     * @param  string           $eventName Event name
     * @param  EventInterface   $event
     * @param  callable|null    $callback
     * @return ResponseCollection
     */
    protected function triggerListeners($eventName, EventInterface $event, callable $callback = null)
    {
        $responses = new ResponseCollection();

        // + operator is faster than array_merge and in this case there are no drawbacks using it
        $listeners = $this->getListeners($eventName)
            + $this->getListeners('*')
            + $this->getSharedListeners($eventName)
            + $this->getSharedListeners('*');

        krsort($listeners);

        foreach ($listeners as $listener) {
            // Using direct de-referencing instead of using "reset" provides a 10% speedup
            // on performance
            $responses->push($listener[0]($event));

            if ($event->isPropagationStopped()
                || ($callback && $callback($responses->last()))
            ) {
                $responses->setStopped(true);
                return $responses;
            }
        }

        return $responses;
    }

    /**
     * Get list of all listeners attached to the shared event manager for
     * identifiers registered by this instance
     *
     * @param  string $eventName
     * @return array
     */
    protected function getSharedListeners($eventName)
    {
        if (null === $this->sharedManager) {
            return array();
        }

        $identifiers = $this->getIdentifiers();
        $listeners   = array();

        // Add wildcard id to the search, if not already added
        if (!in_array('*', $identifiers)) {
            $identifiers[] = '*';
        }

        foreach ($identifiers as $identifier) {
            $listeners = $listeners + $this->sharedManager->getListeners($identifier, $eventName);
        }

        return $listeners;
    }
}
