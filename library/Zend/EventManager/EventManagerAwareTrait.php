<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

trait EventManagerAwareTrait
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $eventIdentifiers = array();

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        if ($eventManager instanceof SharedEventManagerAwareInterface) {
            $eventManager->setIdentifiers(
                array_unique(array_merge([__CLASS__, get_class($this)], $this->eventIdentifiers)
            ));
        }

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
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }
}
