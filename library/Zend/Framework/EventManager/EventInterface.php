<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

interface EventInterface
    extends EventListenerInterface
{
    /**
     * Stop
     *
     * @return EventInterface
     */
    public function stop();

    /**
     * Stopped
     *
     * @return bool
     */
    public function stopped();

    /**
     * Trigger
     *
     * @param ListenerInterface $listener
     * @return bool stopped
     */
    //public function __invoke(ListenerInterface $listener);
}
