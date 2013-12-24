<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerInterface as Listener;

trait ListenerTrait
{
    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $eventName = Event::WILDCARD;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = Event::WILDCARD;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $eventPriority = Listener::DEFAULT_PRIORITY;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = null, $target = null, $priority = null)
    {
        if (null !== $event) {
            $this->setEventName($event);
        }

        if (null !== $target) {
            $this->setEventTarget($target);
        }

        if (null !== $priority) {
            $this->setEventPriority($priority);
        }
    }

    /**
     * @param $name string|array
     * @return Listener
     */
    public function setEventName($name)
    {
        $this->eventName = $name;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return string|array
     */
    public function getEventNames()
    {
        if (is_array($this->eventName)) {
            return $this->eventName;
        }
        return [$this->eventName];
    }

    /**
     * @param $target
     * @return Listener
     */
    public function setEventTarget($target)
    {
        $this->eventTarget = $target;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventTarget()
    {
        return $this->eventTarget;
    }

    /**
     * @return mixed
     */
    public function getEventTargets()
    {
        if (is_array($this->eventTarget)) {
            return $this->eventTarget;
        }
        return [$this->eventTarget];
    }

    /**
     * @param $priority
     * @return Listener
     */
    public function setEventPriority($priority)
    {
        $this->eventPriority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getEventPriority()
    {
        return $this->eventPriority;
    }
}
