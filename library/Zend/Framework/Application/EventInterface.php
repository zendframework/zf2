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
use Zend\Framework\Event\ListenerInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT = 'Application\Event';

    /**
     * @return Request
     */
    public function request();

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request);

    /**
     * @return Response
     */
    public function response();

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response);

    /**
     * @param $content
     * @return self
     */
    public function setResponseContent($content);

    /**
     * @return RouteMatch
     */
    public function routeMatch();

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch);

    /**
     * @return ViewModel
     */
    public function viewModel();

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel);

    /**
     * @param ListenerInterface $listener
     * @param $options
     * @return mixed
     */
    public function __invoke(ListenerInterface $listener, $options = null);
}
