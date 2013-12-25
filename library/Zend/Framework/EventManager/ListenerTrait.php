<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

trait ListenerTrait
{
    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $name = ListenerInterface::WILDCARD;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $target = ListenerInterface::WILDCARD;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $priority = ListenerInterface::PRIORITY;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = null, $target = null, $priority = null)
    {
        if (null !== $event) {
            $this->setName($event);
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
     * @return self
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
     * @return string|array
     */
    public function names()
    {
        if (is_array($this->name)) {
            return $this->name;
        }
        return [$this->name];
    }

    /**
     * @param string|array $target
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return string|array|object
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function targets()
    {
        if (is_array($this->target)) {
            return $this->target;
        }
        return [$this->target];
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * Triggers listener
     *
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event)
    {
    }
}
