<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Controller\Dispatch\EventInterface as DispatchEventInterface;
use Zend\Framework\Controller\RouteMatch\ListenerInterface as ControllerMatchListenerInterface;
use Zend\Framework\Event\EventTrait;
use Zend\Framework\View\Render\EventInterface as RenderEventInterface;
use Zend\Framework\Request\ServicesTrait as Request;
use Zend\Framework\Response\Send\EventInterface as SendEventInterface;
use Zend\Framework\Response\ServicesTrait as ResponseTrait;
use Zend\Framework\Route\Match\ServicesTrait as RouteMatchTrait;
use Zend\Framework\Route\Match\EventInterface as RouteMatchEventInterface;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManager;
use Zend\Framework\View\Render\ListenerInterface as RenderListenerInterface;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModelInterface;

class Event
    implements EventInterface,
               DispatchEventInterface,
               RenderEventInterface,
               RouteMatchEventInterface,
               SendEventInterface
{
    /**
     *
     */
    use EventTrait,
        Request,
        ResponseTrait,
        RouteMatchTrait,
        ViewModel;

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     * @return self
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        $response = $listener($this, $options);

        switch(true) {
            default:
                break;
            case $response instanceof RouteMatch:
                $this->setRouteMatch($response);
                break;
            case $response instanceof Response:
                $this->setResponse($response);
                break;
            case $response instanceof ControllerMatchListenerInterface:
                $this->setController($response);
                break;
            case $response instanceof ViewModelInterface:
                switch(true) {
                    default:
                        $this->setViewModel($response);
                        break 2;
                    case !$response->terminate() && $this->viewModel():
                        $this->addChildViewModel($response);
                        break 2;
                }
                break;
            case $listener instanceof RenderListenerInterface:
                $this->setResponseContent($response);
                break;
        }

        return $response;
    }
}
