<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Error;

use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

interface ListenerInterface
{
    /**
     *
     */
    const TEMPLATE = 'error/404';

    /**
     * @param EventInterface $event
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function __invoke(EventInterface $event, RequestInterface $request, ResponseInterface $response);
}
