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

/**
 * Representation of an event
 *
 * Encapsulates the target context and parameters passed, and provides some
 * behavior for interacting with the event manager.
 */
class Event implements EventInterface
{
    /**
     * Name of wildcard event name
     */
    const WILDCARD_NAME = '*';

    /**
     * @var string Event name
     */
    protected $name = self::WILDCARD_NAME;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var string|object The event target
     */
    protected $target = Listener::WILDCARD_TARGET_NAME;

    /**
     * @var bool Whether or not to stop propagation
     */
    protected $stopPropagation = false;

    /**
     * @var callable Is called when the event's propogation has not been stopped by the listener
     */
    protected $callback;

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param  string $name Event name
     * @param  string|object $target
     * @param  array|ArrayAccess $params
     */
    public function __construct($name = null, $target = null, $callback = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }

        if (null !== $target) {
            $this->setTarget($target);
        }

        if (null !== $callback) {
            $this->setCallback($callback);
        }
    }

    /**
     * @param Callable $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the event name
     *
     * @param  string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param array $listeners
     */
    public function setListeners($listeners)
    {
       $this->listeners = $listeners;
    }

    /**
     * Set the event target/context
     *
     * @param  null|string|object $target
     * @return Event
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Get the event target
     *
     * This may be either an object, or the name of a static method.
     *
     * @return string|object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Stop further event propagation
     *
     * @param  bool $flag
     * @return void
     */
    public function stopPropagation($flag = true)
    {
        $this->stopPropagation = (bool) $flag;
    }

    /**
     * Is propagation stopped?
     *
     * @return bool
     */
    public function propagationIsStopped()
    {
        return $this->stopPropagation;
    }

    /**
     * Determines whether this event should stop propagating (does not set $stopPropagation)
     *
     * @param $callback
     */
    public function setPropagtionCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return array The responses of each listener
     */
    public function getEventResponses()
    {
        return $this->eventResponses;

    }

    /**
     * Invokes listener with this event passed as its only argument.
     * Returns true if this event's propagation has been stopped by the invoked listener.
     * Otherwise, if it exists, returns true if the callback wants to stop this event's propagation.
     * Otherwise, return false.
     *
     * @param $listener
     * @return bool
     */
    public function __invoke($listener)
    {
        $response = $listener($this);

        //$this->eventResponses[] = $response;

        if ($this->stopPropagation) {
            return true;
        }

        if ($this->callback) {
            return (bool) call_user_func($this->callback, $this, $listener, $response);
        }

        return false;
    }
}
