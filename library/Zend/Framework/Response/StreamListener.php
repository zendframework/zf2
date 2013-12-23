<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait;
use Zend\Framework\Response\SendHeadersTrait;
use Zend\Http\Response\Stream;

class StreamListener
    implements StreamListenerInterface
{
    /**
     *
     */
    use ListenerTrait, SendHeadersTrait;

    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $eventName = self::EVENT_RESPONSE;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = self::WILDCARD;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $eventPriority = self::DEFAULT_PRIORITY;

    /**
     * Send the stream
     *
     * @param  Event $event
     * @return $this
     */
    public function sendStream(Event $event)
    {
        if ($event->contentSent()) {
            return $this;
        }
        $response = $event->getResponse();
        $stream   = $response->getStream();
        fpassthru($stream);
        $event->setContentSent();
    }

    /**
     * Send stream response
     *
     * @param  Event $event
     * @return $this
     */
    public function __invoke(Event $event)
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
