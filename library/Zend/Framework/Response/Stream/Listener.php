<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Stream;

use Zend\Framework\Response\EventInterface;
use Zend\Framework\Response\SendHeadersTrait as SendHeaders;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use SendHeaders;

    /**
     * @param EventInterface $event
     * @param Response $response
     * @return $this|mixed
     */
    public function __invoke(EventInterface $event, Response $response)
    {
        $this->sendHeaders($event, $response)
             ->sendStream($event, $response);

        $event->stop();

        return $this;
    }

    /**
     * Send the stream
     *
     * @param  EventInterface $event
     * @return self
     */
    public function sendStream(EventInterface $event)
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
