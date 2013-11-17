<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayAccess;

/**
 * Representation of an event
 */
interface EventInterface
{
    /**
     * Callable used to determine if event propagation should stop
     *
     * @param $callback
     */
    public function setCallback($callback);

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
     * @return void
     */
    public function setName($name);

    /**
     * Get listeners
     *
     * @return null|string|object
     */
    public function getListeners();

    /**
     * Set listeners
     *
     * @param  null|string|object $target
     * @return void
     */
    public function setListeners($listeners);

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
     * @return void
     */
    public function setTarget($target);

    /**
     * Indicate whether or not the parent EventManagerInterface should stop propagating events
     *
     * @param  bool $flag
     * @return void
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
     * @param $listener
     * @return mixed
     */
    public function __invoke($listener);
}
