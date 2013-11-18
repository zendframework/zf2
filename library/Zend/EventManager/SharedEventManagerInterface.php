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
 * Interface for shared event listener collections
 */
interface SharedEventManagerInterface
{
    /**
     * Attach a listener to an event
     *
     * @param  array    $identifiers Identifier(s) for event emitting component(s)
     * @param  string   $eventName
     * @param  callable $listener PHP Callback
     * @param  int      $priority Priority at which listener should execute
     * @return void
     */
    public function attach($identifiers, $eventName, callable $listener, $priority = 1);

    /**
     * Attach a listener aggregate
     *
     * @param  SharedListenerAggregateInterface $aggregate
     * @param  int $priority If provided, a suggested priority for the aggregate to use
     * @return mixed return value of {@link SharedListenerAggregateInterface::attachShared()}
     */
    public function attachAggregate(SharedListenerAggregateInterface $aggregate, $priority = 1);

    /**
     * Detach a listener from an event offered by a given resource
     *
     * @param  string|int $identifier
     * @param  callable   $listener
     * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
     */
    public function detach($identifier, callable $listener);

    /**
     * Detach a listener aggregate
     *
     * @param  SharedListenerAggregateInterface $aggregate
     * @return mixed return value of {@link SharedListenerAggregateInterface::detachShared()}
     */
    public function detachAggregate(SharedListenerAggregateInterface $aggregate);

    /**
     * Retrieve all listeners for the given identifiers and event for a specific event
     *
     * @param  string[] $identifiers
     * @param  string   $eventName
     * @return array
     */
    public function getListeners(array $identifiers, $eventName);

    /**
     * Clear all listeners for the given identifiers and optionally for a specific event
     *
     * @param  string[]    $identifiers
     * @param  null|string $eventName
     * @return void
     */
    public function clearListeners(array $identifiers, $eventName = null);

    /**
     * Retrieve all registered events for a given resource
     *
     * @param  string|int $identifier
     * @return array
     */
    public function getEvents($identifier);
}
