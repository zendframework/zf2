<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Exception;

use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ViewModel;

    /**
     * @param EventInterface $event
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function __invoke(EventInterface $event, RequestInterface $request, ResponseInterface $response)
    {
        $response->setStatusCode(HttpResponse::STATUS_CODE_500);
        return $this->viewModel()->setVariables(['exception' => $event->exception()]);
    }
}
