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
     * Wildcard identifier
     *
     * @var string
     */
    public $name = Event::WILDCARD;

    /**
     * Target (identifiers) of events
     *
     * @var mixed
     */
    public $target = Event::WILDCARD;

    /**
     * @var bool Stop event propagation
     */
    public $propagationStopped = false;

    /**
     * @param string $name
     * @param mixed $target
     */
    public function __construct($name = null, $target = null)
    {
        if (null !== $name) {
            $this->name = $name;
        }

        if (null !== $target) {
            $this->target = $target;
        }
    }

    /**
     * @param Listener $listener
     * @return propagation stopped
     */
    public function __invoke(Listener $listener)
    {
        $listener->__invoke($this);

        return $this->propagationStopped();
    }

    /**
     * @param $name string|array
     * @return Listener
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|array
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param $target
     * @return Listener
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return mixed
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * Stop the event's propagation
     *
     * @return Event
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * Is the event's propagation stopped?
     *
     * @return bool
     */
    public function propagationStopped()
    {
        return $this->propagationStopped;
    }
}
