<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

trait EventTrait
{
    /**
     * Wildcard
     *
     * @var string
     */
    protected $name = EventInterface::WILDCARD;

    /**
     * Target
     *
     * @var string|array
     */
    protected $target = EventInterface::WILDCARD;

    /**
     * Stopped
     *
     * @var bool Stopped
     */
    protected $stopped = false;

    /**
     * Constructor
     *
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
     * Name
     *
     * @return string|array
     */
    public function name()
    {
        return $this->name;
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
     * @return string|array
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * Stop
     *
     * @return self
     */
    public function stop()
    {
        $this->stopped = true;
        return $this;
    }

    /**
     * Stopped
     *
     * @return bool
     */
    public function stopped()
    {
        return $this->stopped;
    }
}
