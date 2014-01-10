<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Stream;

use Zend\Framework\Response\EventInterface as Event;
use Zend\Framework\Response\ListenerTrait as Listener;
use Zend\Framework\Response\SendHeadersTrait as SendHeaders;

trait ListenerTrait
{
    /**
     *
     */
    use Listener,
        SendHeaders;

    /**
     * Send the stream
     *
     * @param  Event $event
     * @return self
     */
    public function sendStream(Event $event)
    {
        if ($event->contentSent()) {
            return $this;
        }

        $response = $event->target();
        $stream   = $response->getStream();

        fpassthru($stream);

        $event->setContentSent();

        return $this;
    }
}
