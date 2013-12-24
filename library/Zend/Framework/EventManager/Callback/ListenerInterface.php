<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\Callback;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
    /**
     * @return callable
     */
    public function getCallback();

    /**
     * Callback used for this listener
     *
     * @param callable $callback
     * @return self
     */
    public function setCallback(callable $callback);
    /**
     * Invokes listener with the event
     *
     * @param Event $event
     * @return mixed
     */
    public function __invoke(Event $event);
}
