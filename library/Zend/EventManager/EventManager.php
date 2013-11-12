<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayAccess;
use ArrayObject;
use Traversable;
use Zend\Stdlib\CallbackHandler;
use Zend\Stdlib\PriorityQueue;

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
     * @var array|PriorityQueue[] Array of PriorityQueue objects
     */
    protected $events = array();

    /**
     * @var string Class representing the event being emitted
     */
    protected $eventClass = 'Zend\EventManager\Event';

    /**
     * Identifiers, used to pull shared signals from SharedEventManagerInterface instance
     *
     * @var array
     */
    protected $identifiers = array();

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
     * @param  SharedEventManagerInterface|null $sharedEventManager
     * @return void
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager = null)
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
     * callback that will respond to that event. A CallbackHandler instance
     * describing the event listener combination will be returned.
     *
     * The last argument indicates a priority at which the event should be
     * executed. By default, this value is 1; however, you may set it for any
     * integer value. Higher values have higher priority (i.e., execute first).
     *
     * You can specify "*" for the event name. In such cases, the listener will
     * be triggered for every event.
     *
     * @param  string|array|ListenerAggregateInterface $event An event or array of event names. If a ListenerAggregateInterface,
     *                                                        proxies to {@link attachAggregate()}.
     * @param  callable                                $callback
     * @param  int                                     $priority If provided, the priority at which to register the callable
     * @return CallbackHandler|mixed CallbackHandler if attaching callable (to allow later unsubscribe); mixed if attaching aggregate
     * @throws Exception\InvalidArgumentException
     */
    public function attach($event, callable $callback, $priority = 1)
    {
        // Array of events should be registered individually, and return an array of all listeners
        if (is_array($event)) {
            $listeners = array();

            foreach ($event as $name) {
                $listeners[] = $this->attach($name, $callback, $priority);
            }

            return $listeners;
        }

        // If we don't have a priority queue for the event yet, create one
        if (empty($this->events[$event])) {
            $this->events[$event] = new PriorityQueue();
        }

        // Create a callback handler, setting the event and priority in its metadata
        $listener = new CallbackHandler($callback, array('event' => $event, 'priority' => $priority));

        // Inject the callback handler into the queue
        $this->events[$event]->insert($listener, $priority);

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
     * @param  int $priority If provided, a suggested priority for the aggregate to use
     * @return mixed return value of {@link ListenerAggregateInterface::attach()}
     */
    public function attachAggregate(ListenerAggregateInterface $aggregate, $priority = 1)
    {
        return $aggregate->attach($this, $priority);
    }

    /**
     * Unsubscribe a listener from an event
     *
     * @param  CallbackHandler $listener
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     */
    public function detach(CallbackHandler $listener)
    {
        $event = $listener->getMetadatum('event');
        if (!$event || empty($this->events[$event])) {
            return false;
        }

        $return = $this->events[$event]->remove($listener);
        if (!$return) {
            return false;
        }

        if (!count($this->events[$event])) {
            unset($this->events[$event]);
        }

        return true;
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
     * @param  string         $eventName
     * @param  EventInterface $event
     * @return ResponseCollection All listener return values
     */
    public function trigger($eventName, EventInterface $event)
    {
        // Initial value of stop propagation flag should be false
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
     * @param  string          $eventName
     * @param  EventInterface  $event
     * @param  callable|null   $callback
     * @return ResponseCollection
     */
    public function triggerUntil($eventName, EventInterface $event, callable $callback = null)
    {
        // Initial value of stop propagation flag should be false
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
    public function getListeners($event)
    {
        if (!isset($this->events[$event])) {
            return new PriorityQueue();
        }

        return $this->events[$event];
    }

    /**
     * {@inheritDoc}
     */
    public function clearListeners($event)
    {
        unset($this->events[$event]);
    }

    /**
     * {@inheritDoc}
     */
    public function setEventClass($class)
    {
        $this->eventClass = (string) $class;
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
     * @param  string           $event Event name
     * @param  EventInterface   $e
     * @param  callable|null    $callback
     * @return ResponseCollection
     */
    protected function triggerListeners($event, EventInterface $e, callable $callback = null)
    {
        $responses = new ResponseCollection;
        $listeners = $this->getListeners($event);

        // Add shared/wildcard listeners to the list of listeners,
        // but don't modify the listeners object
        $sharedListeners         = $this->getSharedListeners($event);
        $sharedWildcardListeners = $this->getSharedListeners('*');
        $wildcardListeners       = $this->getListeners('*');

        if (count($sharedListeners) || count($sharedWildcardListeners) || count($wildcardListeners)) {
            $listeners = clone $listeners;

            // Shared listeners on this specific event
            $this->insertListeners($listeners, $sharedListeners);

            // Shared wildcard listeners
            $this->insertListeners($listeners, $sharedWildcardListeners);

            // Add wildcard listeners
            $this->insertListeners($listeners, $wildcardListeners);
        }

        foreach ($listeners as $listener) {
            $listenerCallback = $listener->getCallback();

            // Trigger the listener's callback, and push its result onto the
            // response collection
            $responses->push($listenerCallback($e));

            // If the event was asked to stop propagating, do so
            if ($e->isPropagationStopped()) {
                $responses->setStopped(true);
                break;
            }

            // If the result causes our validation callback to return true,
            // stop propagation
            if ($callback && $callback($responses->last())) {
                $responses->setStopped(true);
                break;
            }
        }

        return $responses;
    }

    /**
     * Get list of all listeners attached to the shared event manager for
     * identifiers registered by this instance
     *
     * @param  string $event
     * @return PriorityQueue
     */
    protected function getSharedListeners($event)
    {
        if (!$sharedManager = $this->getSharedManager()) {
            return new PriorityQueue();
        }

        $identifiers = $this->getIdentifiers();

        // Add wildcard id to the search, if not already added
        if (!in_array('*', $identifiers)) {
            $identifiers[] = '*';
        }

        $sharedListeners = new PriorityQueue();

        foreach ($identifiers as $id) {
            if (!$listeners = $sharedManager->getListeners($id, $event)) {
                continue;
            }

            $listeners = $listeners->toArray(PriorityQueue::EXTR_BOTH);

            foreach ($listeners as $listener) {
                $sharedListeners->insert($listener['data'], $listener['priority']);
            }
        }

        return $sharedListeners;
    }

    /**
     * Add listeners to the master queue of listeners
     *
     * Used to inject shared listeners and wildcard listeners.
     *
     * @param  PriorityQueue $masterListeners
     * @param  PriorityQueue $listeners
     * @return void
     */
    protected function insertListeners(PriorityQueue $masterListeners, PriorityQueue $listeners)
    {
        $listeners = $listeners->toArray(PriorityQueue::EXTR_BOTH);

        foreach ($listeners as $listener) {
            $masterListeners->insert($listener['data'], $listener['priority']);
        }
    }
}
