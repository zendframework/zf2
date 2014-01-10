<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\Manager\ListenerInterface as EventManager;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

interface EventInterface
    extends Event
{
    /**
     * @return EventManager
     */
    public function eventManager();

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @return bool|Request
     */
    public function request();

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request);

    /**
     * @return bool|object
     */
    public function response();

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response);

    /**
     * @return mixed
     */
    public function result();

    /**
     * @param $result
     * @return self
     */
    public function setResult($result);

    /**
     * @return bool|Router
     */
    public function router();

    /**
     * @param Router $router
     * @return self
     */
    public function setRouter(Router $router);

    /**
     * @return bool|RouteMatch
     */
    public function routeMatch();

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * @return ServiceManager
     */
    public function serviceManager();

    /**
     * Matched route also goes into the SM
     *
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);
    /**
     * @return bool|ViewModel
     */
    public function viewModel();

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel);
}
