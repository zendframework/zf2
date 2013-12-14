<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Traversable;

use Zend\Framework\EventManager\ListenerInterface as EventListener;

/**
 * Representation of an event
 */
interface EventInterface
{
    /**
     * Name of wildcard event name
     */
    const WILDCARD = '*';

    /**
     * Get event name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the event name
     *
     * @param  string $name
     * @return Event
     */
    public function setName($name);

    /**
     * Get listeners
     *
     * @return array|Traversable
     */
    public function getListeners();

    /**
     * Set listeners
     *
     * @param  null|string|object $listeners
     * @return Event
     */
    public function setListeners($listeners);

    /**
     * Add listener
     *
     * @param  object $listener
     * @return Event
     */
    public function addListener($listener);

    /**
     * Get target/context from which event was triggered
     *
     * @return null|string|object
     */
    public function getTarget();

    /**
     * Set the event target/context
     *
     * @param  null|string|object $target
     * @return Event
     */
    public function setTarget($target);

    /**
     * Array of target/context from which event was triggered
     *
     * @return array
     */
    public function getTargets();

    /**
     * Indicate whether or not the parent EventManagerInterface should stop propagating events
     *
     * @param  bool $flag
     * @return Event
     */
    public function stopPropagation($flag = true);

    /**
     * Has this event indicated event propagation should stop?
     *
     * @return bool
     */
    public function propagationIsStopped();

    /**
     * Invokes listener
     *
     * @param EventListener $listener
     * @return mixed
     */
    public function __invoke(EventListener $listener);
}
