<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\ListenerInterface as EventListener;

/**
 * Representation of an event
 *
 * Encapsulates the target context and parameters passed, and provides some
 * behavior for interacting with the event manager.
 */
class Event implements EventInterface
{
    /**
     * @var string Event name
     */
    protected $name = self::WILDCARD;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var string|object The event target
     */
    protected $target = self::WILDCARD;

    /**
     * @var bool Whether or not to stop propagation
     */
    protected $stopPropagation = false;

    /**
     * @var callable called when the event's propogation has not been stopped by the listener
     */
    protected $callback;

    /**
     * @var array
     */
    protected $eventResponses = [];

    /**
     * @param string $name
     * @param mixed $target
     * @param callback $callback
     */
    public function __construct($name = null, $target = null, $callback = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }

        if (null !== $target) {
            $this->setTarget($target);
        }

        if (null == $callback) {
            $callback = $this->getDefaultCallback();
        }

        $this->setCallback($callback);
    }

    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable
     */
    public function getDefaultCallback()
    {
        return null;
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
     * @return Event
     */
    public function setListeners($listeners)
    {
        $this->listeners = $listeners;

        return $this;
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
     * @return array
     */
    public function getTargets()
    {
        if (is_array($this->target)) {
            return $this->target;
        }

        return [$this->target];
    }

    /**
     * Stop further event propagation
     *
     * @param  bool $flag
     * @return Event
     */
    public function stopPropagation($flag = true)
    {
        $this->stopPropagation = (bool) $flag;

        return $this;
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
     * @return array The responses of each listener
     */
    public function getEventResponses()
    {
        return $this->eventResponses;
    }

    /**
     * Invokes listener with this event passed as its only argument.
     *
     * @param EventListener $listener
     * @return bool
     */
    public function __invoke(EventListener $listener)
    {
        $response = $listener($this);

        $this->eventResponses[] = $response;

        if ($this->callback) {
            call_user_func($this->callback, $this, $listener, $response);
        }
    }
}
