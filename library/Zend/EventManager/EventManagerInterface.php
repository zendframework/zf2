<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use Traversable;

/**
 * Interface for messengers
 */
interface EventManagerInterface extends SharedEventManagerAwareInterface
{
    /**
     * Attach a listener to an event
     *
     * @param  string   $event
     * @param  callable $listener
     * @param  int      $priority Priority at which to register listener
     * @return callable
     */
    public function attach($event, callable $listener, $priority = 1);

    /**
     * Attach a listener aggregate
     *
     * @param  ListenerAggregateInterface $aggregate
     * @param  int                        $priority If provided, a suggested priority for the aggregate to use
     * @return mixed
     */
    public function attachAggregate(ListenerAggregateInterface $aggregate, $priority = 1);

    /**
     * Detach an event listener
     *
     * @param  callable $listener
     * @return bool
     */
    public function detach(callable $listener);

    /**
     * Detach a listener aggregate
     *
     * @param  ListenerAggregateInterface $aggregate
     * @return bool
     */
    public function detachAggregate(ListenerAggregateInterface $aggregate);

    /**
     * Trigger an event
     *
     * @param  string         $eventName
     * @param  EventInterface $event
     * @return ResponseCollection
     */
    public function trigger($eventName, EventInterface $event);

    /**
     * Trigger an event until the given callback returns a boolean false
     *
     * @param  string         $eventName
     * @param  EventInterface $event
     * @param  callable|null  $callback
     * @return ResponseCollection
     */
    public function triggerUntil($event, EventInterface $event, callable $callback = null);

    /**
     * Get a list of events for which this collection has listeners
     *
     * @return array
     */
    public function getEvents();

    /**
     * Retrieve a list of listeners registered to a given event
     *
     * @param  string $eventName
     * @return array
     */
    public function getListeners($eventName);

    /**
     * Clear all listeners for a given event
     *
     * @param  string $eventName
     * @return void
     */
    public function clearListeners($eventName);

    /**
     * Set the identifiers (overrides any currently set identifiers)
     *
     * @param  string|int|array|Traversable $identifiers
     * @return void
     */
    public function setIdentifiers($identifiers);

    /**
     * Add some identifier(s) (appends to any currently set identifiers)
     *
     * @param  string|int|array|Traversable $identifiers
     * @return void
     */
    public function addIdentifiers($identifiers);

    /**
     * Get the identifier(s) for this EventManager
     *
     * @return array
     */
    public function getIdentifiers();
}
