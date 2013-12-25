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
     * Stop propagation
     *
     * @return EventInterface
     */
    public function stopPropagation();

    /**
     * Whether propagation has stopped
     *
     * @return bool
     */
    public function propagation();

    /**
     * Triggers event
     *
     * @param ListenerInterface $listener
     * @return propagation stopped
     */
    public function __invoke(ListenerInterface $listener);
}
