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

trait EventTrait
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
     * @var bool Whether or not to stop propagation
     */
    protected $eventStopPropagation = false;

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
