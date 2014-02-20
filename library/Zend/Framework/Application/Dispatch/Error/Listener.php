<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch\Error;

use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Http\Response as HttpResponse;

class Listener
    implements ListenerInterface
{
    /**
     * @param EventInterface $event
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function __invoke(EventInterface $event, RequestInterface $request, ResponseInterface $response)
    {
        $response->setStatusCode(HttpResponse::STATUS_CODE_500)
                 ->setContent($response->getReasonPhrase());
    }
}
