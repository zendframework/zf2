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
class EventManager extends FastEventManager implements SharedEventManagerAwareInterface
{
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
        if ($identifiers) {
            $this->setIdentifiers($identifiers);
        }
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
     * @param  string   $eventName An event or array of event names
     * @param  callable $listener
     * @param  int      $priority If provided, the priority at which to register the callable
     * @return callable if attaching callable (to allow later unsubscribe)
     */
    public function attach($eventName, callable $listener, $priority = 1)
    {
        // The '.0' is a hack that allows to circumvent the fact that array_merge remove
        // any numeric key
        $this->events[$eventName][(int) $priority . '.0'][] = $listener;

        return $listener;
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners($eventName)
    {
        // retrieve listeners
        $listeners = isset($this->events[$eventName]) ? $this->events[$eventName] : [];

        // retrieve wildcard listeners
        $wildcardListeners = isset($this->events['*']) ? $this->events['*'] : [];

        // retrieve shared manager listeners
        $sharedListeners = (null !== $this->sharedManager)
            ? $this->sharedManager->getListeners($this->identifiers, $eventName)
            : [];

        // merge
        $listeners = array_merge_recursive($listeners, $wildcardListeners, $sharedListeners);
        krsort($listeners, SORT_NUMERIC);

        return $listeners;
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
}
