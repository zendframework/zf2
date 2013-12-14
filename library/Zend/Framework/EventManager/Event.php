<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\ListenerInterface as EventListener;

use Zend\Framework\EventManager\EventInterface;

/**
 * Representation of an event
 *
 * Encapsulates the target context and parameters passed, and provides some
 * behavior for interacting with the event manager.
 */
class Event
    implements EventInterface
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
     * @var array
     */
    protected $eventResponses = [];

    /**
     * @param string $name
     * @param mixed $target
     */
    public function __construct($name = null, $target = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }

        if (null !== $target) {
            $this->setTarget($target);
        }
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

    public function addListener($listener)
    {
        $this->listeners[] = $listener;
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
     * @param EventListener $listener
     * @return void
     */
    public function __invoke(EventListener $listener)
    {
        $listener($this);
    }
}
