<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\ResponseSender;

use Zend\Http\Header\MultipleHeaderInterface;
use Zend\Http\Response\Stream;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\ServiceManager\FactoryInterface;

class SimpleStreamResponseSender
    extends AbstractResponseSender
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_RESPONSE;

    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * Send the stream
     *
     * @param  Event $event
     * @return SimpleStreamResponseSender
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
     * @return SimpleStreamResponseSender
     */
    public function __invoke(Event $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Stream) {
            return $this;
        }

        $this->sendHeaders($event);
        $this->sendStream($event);
        $event->stopPropagation(true);
        return $this;
    }
}
