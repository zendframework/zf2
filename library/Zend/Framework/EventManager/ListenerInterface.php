<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

interface ListenerInterface
    extends EventListenerInterface
{
    /**
     * Priority default
     *
     */
    const PRIORITY = 0;

    /**
     * Priority
     *
     * @return int
     */
    public function priority();

    /**
     * Priority set
     *
     * @param int $priority
     * @return self
     */
    public function setPriority($priority);

    /**
     * Names
     *
     * @return array
     */
    public function names();

    /**
     * Targets
     *
     * @return array
     */
    public function targets();

    // Listeners specify event interface
    ///**
    // * Trigger
    // *
    // * @param EventInterface $event
    // */
    //public function __invoke(EventInterface $event)
    //{
    //}
}
