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

interface EventInterface
    extends EventListenerInterface
{
    /**
     * Stop the event's propagation
     *
     * @return Event
     */
    public function stopEventPropagation();

    /**
     * Is the event's propagation stopped?
     *
     * @return bool
     */
    public function isEventPropagationStopped();

    /**
     * Invokes the event with the listener that the event will invoke
     *
     * @param Listener $listener
     * @return mixed
     */
    public function __invoke(Listener $listener);
}
