<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Http;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Http\Response;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_RESPONSE, $target = null, $priority = null)
    {
        $this->eventName = $event;
    }

    /**
     * Send HTTP response
     *
     * @param  Event $event
     * @return self
     */
    public function __invoke(Event $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return $this;
        }

        $this->sendHeaders($event)
             ->sendContent($event);

        $event->stopEventPropagation();

        return $this;
    }
}
