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
 */
class SharedEventManager implements SharedEventManagerInterface
{
    /**
     * Identifiers that are mapped to listeners
     *
     * @var array|EventManagerInterface[]
     */
    protected $identifiers = array();

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
        $identifiers = (array) $identifiers;

        foreach ($identifiers as $identifier) {
            $this->identifiers[$identifier][$eventName][(int) $priority][] = $listener;
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
     * @param  string|int $id
     * @param  callable   $listener
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     */
    public function detach($id, callable $listener)
    {
        if (!isset($this->identifiers[$id])) {
            return false;
        }

        return $this->identifiers[$id]->detach($listener);
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
     * Retrieve all listeners for a given identifier and event
     *
     * @param  string|int $identifier
     * @param  string|int $eventName
     * @return array
     */
    public function getListeners($identifier, $eventName)
    {
        if (isset($this->identifiers[$identifier]) && isset($this->identifiers[$identifier][$eventName])) {
            return $this->identifiers[$identifier][$eventName];
        }

        return array();
    }

    /**
     * Clear all listeners for a given identifier, optionally for a specific event
     *
     * @param  string|int $id
     * @param  null|string $event
     * @return void
     */
    public function clearListeners($id, $event = null)
    {
        if (!isset($this->identifiers[$id])) {
            return;
        }

        if (null === $event) {
            unset($this->identifiers[$id]);
        }

        $this->identifiers[$id]->clearListeners($event);
    }

    /**
     * Retrieve all registered events for a given resource
     *
     * @param  string|int $id
     * @return array
     */
    public function getEvents($id)
    {
        if (!isset($this->identifiers[$id])) {
            // Check if there are any id wildcards listeners
            if ('*' !== $id && isset($this->identifiers['*'])) {
                return $this->identifiers['*']->getEvents();
            }

            return false;
        }

        return $this->identifiers[$id]->getEvents();
    }
}
