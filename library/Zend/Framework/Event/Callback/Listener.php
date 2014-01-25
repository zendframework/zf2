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

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * Trigger
     *
     * @param Event $event
     * @param $response
     * @return mixed
     */
    public function trigger(Event $event, $response)
    {
        return call_user_func($this->callback, $event);
    }
}
