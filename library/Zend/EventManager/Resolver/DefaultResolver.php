<?php

namespace Zend\EventManager\Resolver;

use Zend\EventManager\Exception\InvalidArgumentException;

class DefaultResolver implements ResolverInterface, EventClassAwareInterface
{
    /**
     * @var string
     */
    protected $eventClass = 'Zend\EventManager\Event';

    /**
     * Set the event class to utilize
     *
     * @param $eventClass
     * @throws \Zend\EventManager\Exception\InvalidArgumentException
     * @return $this
     */
    public function setEventClass($eventClass)
    {
        if (! class_implements($eventClass, 'Zend\Event\EventInterface')) {
            throw new InvalidArgumentException(sprintf(
                'Expecting a class that implements Zend\Event\EventInterface, %s given', $eventClass
            ));
        }
        $this->eventClass = $eventClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * @param $eventName
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName = null)
    {
        return new $this->eventClass($eventName);
    }
}