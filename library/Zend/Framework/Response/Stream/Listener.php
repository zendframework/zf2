<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Stream;

use Zend\Framework\EventManager\EventInterface;
use Zend\Http\Response\Stream;

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
     * Send stream response
     *
     * @param  EventInterface $event
     * @return self
     */
    public function __invoke(EventInterface $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Stream) {
            return $this;
        }

        $this->sendHeaders($event);
        $this->sendStream($event);

        $event->stopEventPropagation();

        return $this;
    }
}
