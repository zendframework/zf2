<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Application\View\ListenerInterface as ViewListenerInterface;
use Zend\Framework\Event\EventTrait;
use Zend\Framework\Request\ServicesTrait as Request;
use Zend\Framework\Response\ServicesTrait as ResponseTrait;
use Zend\Framework\Route\ServicesTrait as RouteTrait;
use Zend\Framework\Service\ManagerInterface as ServiceManager;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ModelInterface as ViewModelInterface;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        Request,
        ResponseTrait,
        RouteTrait,
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
     * @param callable $listener
     * @param $options
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
            case $response && $listener instanceof ViewListenerInterface:
                $this->setResponseContent($response);
                break;
        }

        return $response;
    }
}
