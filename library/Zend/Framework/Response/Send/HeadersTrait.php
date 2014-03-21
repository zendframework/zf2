<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Send;

use Zend\Http\Header\MultipleHeaderInterface;
use Zend\Stdlib\ResponseInterface;

trait HeadersTrait
{
    /**
     * Send HTTP headers
     *
     * @param EventInterface $event
     * @param ResponseInterface $response
     * @return self
     */
    protected function sendHeaders(EventInterface $event, ResponseInterface $response)
    {
        //if (headers_sent() || $event->headersSent()) {
        if (headers_sent()) {
            return $this;
        }

        foreach ($response->getHeaders() as $header) {
            if ($header instanceof MultipleHeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }

        $status = $response->renderStatusLine();
        header($status);

        //$event->setHeadersSent();

        return $this;
    }
}
