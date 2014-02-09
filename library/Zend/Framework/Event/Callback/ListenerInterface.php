<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Callback;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
    /**
     * @return callable
     */
    public function callback();

    /**
     * @param callable $callback
     * @return self
     */
    public function setCallback(callable $callback);

    /**
     * @param Event $event
     * @param $response
     * @return mixed
     */
    public function __invoke(Event $event, $response);
}
