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
    protected $events = [];

    /**
     * Identifiers, used to pull shared signals from SharedEventManagerInterface instance
     *
     * @var array
     */
    protected $identifiers = [];

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
        $this->events[(string) $event][(int) $priority . '.0'][] = $listener;

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
     * This method is quite inefficient as it needs to traverse each queue, so use with care! If you are that
     * worried about performance, you should always filter by the event name so that less work is done
     *
     * @param  callable $listener
     * @param  string   $eventName
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     */
    public function detach(callable $listener, $eventName = '')
    {
        if (!empty($eventName) && isset($this->events[$eventName])) {
            foreach ($this->events[$eventName] as &$listeners) {
                if (($key = array_search($listener, $listeners, true)) !== false) {
                    unset($listeners[$key]);
                    return true;
                }
            }

            return false;
        }

        foreach ($this->events as &$event) {
            foreach ($event as &$listeners) {
                if (($key = array_search($listener, $listeners, true)) !== false) {
                    unset($listeners[$key]);
                    return true;
                }
            }
        }

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
     * Trigger all listeners for a given event (optionally until a callback evaluates to true)
     *
     * @param  string              $eventName
     * @param  EventInterface|null $event
     * @param  callable|null       $callback
     * @return ResponseCollection All listener return values
     */
    public function trigger($eventName, EventInterface $event = null, callable $callback = null)
    {
        // Initial value of stop propagation flag should be false
        $event = $event ?: new Event();
        $event->stopPropagation(false);

        $responses = array();

        // We cannot use union (+) operator as it merges numeric indexed keys
        $listeners = array_merge_recursive(
            $this->getListeners($eventName),
            $this->getListeners('*'),
            $this->getSharedListeners($eventName),
            $this->getSharedListeners('*')
        );

        krsort($listeners);

        foreach ($listeners as $listenersByPriority) {
            foreach ($listenersByPriority as $listener) {
                $lastResponse = $listener($event);
                $responses[]  = $lastResponse;

                if ($event->isPropagationStopped()
                    || ($callback && $callback($lastResponse))
                ) {
                    $responseCollection = new ResponseCollection($responses);
                    $responseCollection->setStopped(true);

                    return $responseCollection;
                }
            }
        }

        return new ResponseCollection($responses);
    }

    /**
     * {@inheritDoc}
     */
    public function getEventNames()
    {
        return array_keys($this->events);
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners($eventName)
    {
        return isset($this->events[$eventName]) ? $this->events[$eventName] : [];
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
        if ($identifiers instanceof Traversable) {
            $identifiers = iterator_to_array($identifiers);
        }

        $this->identifiers = (array) $identifiers;
    }

    /**
     * {@inheritDoc}
     */
    public function addIdentifiers($identifiers)
    {
        if ($identifiers instanceof Traversable) {
            $identifiers = iterator_to_array($identifiers);
        }

        $this->identifiers = array_unique(array_merge($this->identifiers, $identifiers));
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

        $identifiers = $this->identifiers;

        // Add wildcard id to the search, if not already added
        if (!in_array('*', $identifiers, true)) {
            $identifiers[] = '*';
        }

        return $this->sharedManager->getListeners($identifiers, $eventName);
    }
}
