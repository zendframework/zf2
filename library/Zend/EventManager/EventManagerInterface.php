<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

/**
 * Interface for messengers
 */
interface EventManagerInterface
{
    /**
     * @param ListenerInterface $listener
     */
    public function attach($listener);

    /**
     * @param ListenerInterface $listener
     */
    public function detach($listener);

    /**
     * @param EventInterface $event
     * @return array|Traversable
     */
    public function getEventListeners($event);

    /**
     * @param EventInterface $event
     */
    public function __invoke($event);
}
