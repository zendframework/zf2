<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Send;

use Zend\Stdlib\ResponseInterface;

trait StreamTrait
{
    /**
     * Send the stream
     *
     * @param EventInterface $event
     * @param ResponseInterface $response
     * @return self
     */
    protected function sendStream(EventInterface $event, ResponseInterface $response)
    {
        if ($event->contentSent()) {
            return $this;
        }

        $stream = $response->getStream();

        fpassthru($stream);

        $event->setContentSent();

        return $this;
    }
}
