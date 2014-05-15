<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

/**
 * Shared/contextual EventManager
 *
 * Allows attaching to EMs composed by other classes without having an instance first.
 * The assumption is that the SharedEventManager will be injected into EventManager
 * instances, and then queried for additional listeners when triggering an event.
 *
 * Implementation note: this is performance sensitive code, please do not change unless you have
 * carefully benchmarked it
 */
class SharedEventManager implements SharedEventManagerInterface
{
    /**
     * Identifiers that are mapped to listeners
     *
     * @var array
     */
    protected $identifiers = [];

    /**
     * Attach a listener to an event
     *
     * Allows attaching a callback to an event offered by one or more
     * identifying components
     *
     * @param  string|array $identifiers Identifier(s) for event emitting component(s)
     * @param  string       $eventName
     * @param  callable     $listener PHP Callback
     * @param  int          $priority Priority at which listener should execute
     * @return callable
     */
    public function attach($identifiers, $eventName, callable $listener, $priority = 1)
    {
        foreach ((array) $identifiers as $identifier) {
            $this->identifiers[$identifier][$eventName][(int) $priority . '.0'][] = $listener;
        }

        return $listener;
    }

    /**
     * Attach a listener aggregate
     *
     * Listener aggregates accept an EventManagerInterface instance, and call attachShared()
     * one or more times, typically to attach to multiple events using local
     * methods.
     *
     * @param  SharedListenerAggregateInterface $aggregate
     * @param  int $priority If provided, a suggested priority for the aggregate to use
     * @return mixed return value of {@link SharedListenerAggregateInterface::attachShared()}
     */
    public function attachAggregate(SharedListenerAggregateInterface $aggregate, $priority = 1)
    {
        return $aggregate->attachShared($this, $priority);
    }

    /**
     * Detach a listener from an event offered by a given resource
     *
     * @param  string|int $identifier
     * @param  callable   $listener
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     */
    public function detach($identifier, callable $listener)
    {
        if (isset($this->identifiers[$identifier])) {
            foreach ($this->identifiers[$identifier] as &$event) {
                foreach ($event as &$listeners) {
                    if (($key = array_search($listener, $listeners, true)) !== false) {
                        unset($listeners[$key]);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Detach a listener aggregate
     *
     * Listener aggregates accept an SharedEventManagerInterface instance, and call detachShared()
     * of all previously attached listeners.
     *
     * @param  SharedListenerAggregateInterface $aggregate
     * @return mixed return value of {@link SharedListenerAggregateInterface::detachShared()}
     */
    public function detachAggregate(SharedListenerAggregateInterface $aggregate)
    {
        return $aggregate->detachShared($this);
    }

    /**
     * Retrieve all listeners for the given identifiers and for a specific event
     *
     * @param  string[] $identifiers
     * @param  string   $eventName
     * @return array
     */
    public function getListeners(array $identifiers, $eventName)
    {
        $listeners = [];

        // We detect if there is a wildcard identifier so that we can retrieve its listeners, and
        // remove the conditional in the foreach
        $wildcardIdentifierListeners = isset($identifiers['*']) ? $identifiers['*'] : null;
        unset($identifiers['*']);

        foreach ($identifiers as $identifier) {
            if (isset($this->identifiers[$identifier][$eventName]) && $eventName !== '*') {
                $listeners = array_merge_recursive($listeners, $this->identifiers[$identifier][$eventName]);
            }

            if (isset($this->identifiers[$identifier]['*'])) {
                $listeners = array_merge_recursive($listeners, $this->identifiers[$identifier]['*']);
            }
        }

        // merge listeners attached to wildcard identifiers
        if (null !== $wildcardIdentifierListeners) {
            if (isset($wildcardIdentifierListeners[$eventName]) && $eventName !== '*') {
                $listeners = array_merge_recursive($listeners, $wildcardIdentifierListeners[$eventName]);
            }

            if (isset($wildcardIdentifierListeners['*'])) {
                $listeners = array_merge_recursive($listeners, $wildcardIdentifierListeners['*']);
            }
        }

        return $listeners;
    }

    /**
     * Clear all listeners for the given identifiers and optionally for a specific event
     *
     * @param  string[]    $identifiers
     * @param  null|string $eventName
     * @return void
     */
    public function clearListeners(array $identifiers, $eventName = null)
    {
        foreach ($identifiers as $identifier) {
            if (null === $eventName) {
                unset($this->identifiers[$identifier]);
            } else {
                unset($this->identifiers[$identifier][$eventName]);
            }
        }
    }

    /**
     * Retrieve all registered events for a given resource
     *
     * @param  string|int $identifier
     * @return array
     */
    public function getEventNames($identifier)
    {
        if (!isset($this->identifiers[$identifier])) {
            // Check if there are any id wildcards listeners
            if ('*' !== $identifier && isset($this->identifiers['*'])) {
                return array_keys($this->identifiers['*']);
            }

            return false;
        }

        return array_keys($this->identifiers[$identifier]);
    }
}
