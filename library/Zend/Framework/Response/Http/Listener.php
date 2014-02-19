<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Http;

use Zend\Framework\Response\EventInterface;
use Zend\Framework\Response\SendContentTrait as SendContent;
use Zend\Framework\Response\SendHeadersTrait as SendHeaders;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use SendContent,
        SendHeaders;

    /**
     * Send HTTP response
     *
     * @param  EventInterface $event
     * @param $response
     * @return self
     */
    public function __invoke(EventInterface $event, Response $response)
    {
        $this->sendHeaders($event, $response)
             ->sendContent($event, $response);

        return true;
    }
}
