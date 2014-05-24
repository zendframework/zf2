<?php

namespace Zend\EventManager\Provider;

use Zend\EventManager\EventInterface;

/**
 * Use EventClass or Event instance as prototype
 */
class PrototypeProvider extends DefaultProvider
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
     * @param $target
     * @param array $parameters
     * @return \Zend\EventManager\Event|\Zend\EventManager\EventInterface
     */
    public function get($eventName, $target = null, $parameters = array())
    {
        $event = $this->getEventPrototype();
        $event->setName($eventName);
        $event->setTarget($target);
        $event->setParams($parameters);

        return $event;
    }
}