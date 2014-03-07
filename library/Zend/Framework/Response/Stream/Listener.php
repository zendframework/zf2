<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Stream;

use Zend\Framework\Response\EventInterface;
use Zend\Framework\Response\SendHeadersTrait as SendHeaders;
use Zend\Framework\Response\SendStreamTrait as SendStream;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use SendHeaders,
        SendStream;

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
}
