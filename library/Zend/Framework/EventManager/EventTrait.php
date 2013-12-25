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
     * Wildcard
     *
     * @var string
     */
    public $name = Event::WILDCARD;

    /**
     * Target (identifiers)
     *
     * @var mixed
     */
    public $target = Event::WILDCARD;

    /**
     * @var bool Propagation
     */
    public $propagation = true;

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
     * Triggers event
     *
     * @param Listener $listener
     * @return bool Propagation
     */
    public function __invoke(Listener $listener)
    {
        $listener->__invoke($this);

        return $this->propagation;
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
     * @param $target
     * @return self
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
     * Stop propagation
     *
     * @return self
     */
    public function stopPropagation()
    {
        $this->propagation = false;
        return $this;
    }

    /**
     * Whether propagation has stopped
     *
     * @return bool
     */
    public function propagation()
    {
        return $this->propagation;
    }
}
