<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

trait EventTrait
{
    /**
     * Wildcard
     *
     * @var string
     */
    public $name = EventInterface::WILDCARD;

    /**
     * Target (identifiers)
     *
     * @var string|array
     */
    public $target = EventInterface::WILDCARD;

    /**
     * @var bool Stopped
     */
    public $stopped = false;

    /**
     * @param string $name
     * @param string|array $target
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
     * Triggers event
     *
     * @param ListenerInterface $listener
     * @return bool Stopped
     */
    public function __invoke(ListenerInterface $listener)
    {
        $listener->__invoke($this);

        return $this->stopped;
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
     * @param string|array $target
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return string|array
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * Stop event
     *
     * @return self
     */
    public function stop()
    {
        $this->stopped = true;
        return $this;
    }

    /**
     * If event stopped
     *
     * @return bool
     */
    public function stopped()
    {
        return $this->stopped;
    }
}
