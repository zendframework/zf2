<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch\Error;

use Zend\Framework\Controller\View\Model\ServiceTrait as ControllerViewModel;
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
    use ControllerViewModel,
        ViewModel;

    /**
     * @param EventInterface $event
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function __invoke(EventInterface $event, RequestInterface $request, ResponseInterface $response)
    {
        $response->setStatusCode(HttpResponse::STATUS_CODE_500);

        $viewModel = $this->controllerViewModel()
                          ->setVariables(['exception' => $event->exception()]);

        return $this->viewModel()->addChild($viewModel);
    }
}
