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

trait EventTrait
{
    /**
     * @var string
     */
    public $eventName = Event::WILDCARD;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    public $eventTarget = Event::WILDCARD;

    /**
     * @var bool Whether or not to stop propagation
     */
    public $eventStopPropagation = false;

    /**
     * @param string $name
     * @param mixed $target
     */
    public function __construct($name = null, $target = null)
    {
        if (null !== $name) {
            $this->eventName = $name;
        }

        if (null !== $target) {
            $this->eventTarget = $target;
        }
    }

    /**
     * @param Listener $listener
     * @return bool
     */
    public function __invoke(Listener $listener)
    {
        $listener->__invoke($this);

        return $this->eventStopPropagation;
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
     * Stop the event's propagation
     *
     * @return Event
     */
    public function stopEventPropagation()
    {
        $this->eventStopPropagation = true;
        return $this;
    }

    /**
     * Is the event's propagation stopped?
     *
     * @return bool
     */
    public function isEventPropagationStopped()
    {
        return $this->eventStopPropagation;
    }
}
