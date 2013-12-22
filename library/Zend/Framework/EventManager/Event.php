<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventTrait;
use Zend\Framework\EventManager\ListenerInterface as Listener;

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
     *
     */
    use EventTrait;

    /**
     * @param string $name
     * @param mixed $target
     */
    public function __construct($name = null, $target = null)
    {
        if (null !== $name) {
            $this->setEventName($name);
        }

        if (null !== $target) {
            $this->setEventTarget($target);
        }
    }

    /**
     * @param Listener $listener
     * @return bool
     */
    public function __invoke(Listener $listener)
    {
        $listener($this);

        return $this->eventStopPropagation;
    }
}
