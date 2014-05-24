<?php

namespace Zend\EventManager\Resolver;

use Zend\EventManager\EventInterface;

/**
 * Use EventClass or Event instance as prototype
 */
class PrototypeResolver extends DefaultResolver
{
    /**
     * @var \Zend\EventManager\Event
     */
    protected $eventPrototype;

    /**
     * @param EventInterface $event
     * @return $this
     */
    public function setEventPrototype(EventInterface $event)
    {
        $this->eventPrototype = $event;
        return $this;
    }

    /**
     * Use prototype here, for an event skeleton instance
     */
    public function getEventPrototype()
    {
        if (null === $this->eventPrototype) {
            $this->eventPrototype = new $this->eventClass();
        }

        return clone $this->eventPrototype;
    }

    /**
     * @param $eventName
     * @return \Zend\EventManager\Event|\Zend\EventManager\EventInterface
     */
    public function get($eventName = null)
    {
        $event = $this->getEventPrototype();
        if (null !== $eventName) {
            $event->setName($eventName);
        }
        return $event;
    }
}