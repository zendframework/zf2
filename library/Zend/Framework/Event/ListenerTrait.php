<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

trait ListenerTrait
{
    /**
     * Name
     *
     * @var string|array
     */
    protected $name = ListenerInterface::WILDCARD;

    /**
     * Target
     *
     * @var mixed
     */
    protected $target = ListenerInterface::WILDCARD;

    /**
     * Priority
     *
     * @var int
     */
    protected $priority = ListenerInterface::PRIORITY;

    /**
     * Constructor
     *
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
     * @param $target
     * @return bool
     */
    public function matchTarget($target)
    {
        return $this->target == self::WILDCARD
                || $target == $this->target
                    || $target instanceof $this->target
                        || \is_subclass_of($target, $this->target);
    }

    /**
     * Name set
     *
     * @param $name string|array
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Name
     *
     * @return string|array
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Priority set
     *
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Priority
     *
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * Target set
     *
     * @param string|array $target
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Target
     *
     * @return string|array|object
     */
    public function target()
    {
        return $this->target;
    }
}
