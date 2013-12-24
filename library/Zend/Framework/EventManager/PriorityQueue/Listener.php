<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\PriorityQueue;

use Zend\Framework\EventManager\EventInterface as Event;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param Event $event
     * @return bool event propagation was stopped
     */
    public function __invoke(Event $event)
    {
        foreach($this->getEventListeners($event) as $listener) {
            //var_dump(get_class($event).' :: '.$event->getEventName().' :: '.get_class($listener));
            if ($event($listener)) {
                return true;
            }
        }

        return false; //propagation was not stopped
    }
}
