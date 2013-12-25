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
     * Stop event
     *
     * @return EventInterface
     */
    public function stop();

    /**
     * If event stopped
     *
     * @return bool
     */
    public function stopped();

    /**
     * Triggers event
     *
     * @param ListenerInterface $listener
     * @return bool stopped
     */
    public function __invoke(ListenerInterface $listener);
}
