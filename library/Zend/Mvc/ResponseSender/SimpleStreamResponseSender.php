<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\ResponseSender;

use Zend\Http\Header\MultipleHeaderInterface;
use Zend\Http\Response\Stream;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage ResponseSender
 */
class SimpleStreamResponseSender extends AbstractResponseSender implements ResponseSenderInterface
{

    /**
     * Send the stream
     *
     * @param SendResponseEvent $event
     * @return SimpleStreamResponseSender
     */
    public function sendStream(SendResponseEvent $event)
    {
        if ($event->contentSent()) {
            return $this;
        }
        $response = $event->getResponse();
        $stream = $response->getStream();
        fpassthru($stream);
        $event->setContentSent();
    }

    /**
     * Send stream response
     *
     * @param SendResponseEvent $event
     * @return SimpleStreamResponseSender
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response instanceof Stream) {
            $this->sendHeaders($event);
            $this->sendStream($event);
            $event->stopPropagation(true);
        }
        return $this;
    }

}
