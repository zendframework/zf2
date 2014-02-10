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
use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\ResultTrait as Result;
use Zend\Framework\Request\ServicesTrait as Request;
use Zend\Framework\Response\ServicesTrait as Response;
use Zend\Framework\Route\ServicesTrait as Route;
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
        Response,
        Result,
        Route,
        ViewModel;

    /**
     * @var string
     */
    protected $name = self::EVENT;

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
     * @param ListenerInterface $listener
     * @param $options
     * @return mixed
     */
    public function __invoke(ListenerInterface $listener, $options = null)
    {
        $response = $listener->__invoke($this, $options);

        switch(true) {
            default:
                break;
            case $response instanceof RouteMatch:
                $this->setRouteMatch($response);
                break;
            case $response instanceof ViewModelInterface:
                $this->setViewModel($response);
                break;
            case $listener instanceof ViewListenerInterface:
                $this->setResponseContent($response);
                break;
        }

        return $response;
    }
}
