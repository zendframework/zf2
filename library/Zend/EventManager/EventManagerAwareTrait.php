<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use Traversable;

trait EventManagerAwareTrait
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var array|string|object|null
     */
    protected $eventIdentifier = null;

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $identifiers = array(__CLASS__, get_class($this));

        if ((is_string($this->eventIdentifier))
            || is_array($this->eventIdentifier)
            || ($this->eventIdentifier instanceof Traversable)
        ) {
            $identifiers = array_merge($identifiers, (array) $this->eventIdentifier);
        } elseif (is_object($this->eventIdentifier)) {
            $identifiers[] = $this->eventIdentifier;
        }

        // silently ignore invalid event identifiers types

        $eventManager->setIdentifiers($identifiers);

        $this->eventManager = $eventManager;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->eventManager instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }
}
