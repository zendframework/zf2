<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

class Listener implements ListenerInterface
{
    /**
     *
     */
    const WILDCARD_TARGET_NAME = '*';

    /**
     *
     */
    const DEFAULT_PRIORITY = 1;

    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $name;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $priority = self::DEFAULT_PRIORITY;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $target = self::WILDCARD_TARGET_NAME;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = null, $target = null, $priority = null)
    {
        var_dump($event, $target, $priority);
        if (null !== $event) {
            $this->setEventName($event);
        }

        if (null !== $target) {
            $this->setTarget($target);
        }

        if (null !== $priority) {
            $this->setPriority($priority);
        }
    }

    /**
     * @param $name string|array
     */
    public function setEventName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|array
     */
    public function getEventName()
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getEventNames()
    {
        if (is_array($this->name)) {
            return $this->name;
        }

        return [$this->name];
    }

    /**
     * @param $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param $target
     * @return mixed
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return mixed
     */
    public function getTargets()
    {
        if (is_array($this->target)) {
            return $this->target;
        }

        return [$this->target];
    }

    /**
     * @param $event
     * @return mixed
     */
    public function __invoke($event)
    {
    }
}
