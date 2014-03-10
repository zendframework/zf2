<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $cm;

    /**
     * @return ManagerInterface
     */
    public function controllerManager()
    {
        return $this->cm;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param RouteMatch $routeMatch
     * @param null $controller
     * @return mixed
     */
    public function error(Request $request, Response $response, RouteMatch $routeMatch = null, $controller = null)
    {
        return $this->cm->error($request, $response, $routeMatch, $controller);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Exception $exception
     * @return mixed
     */
    public function exception(Request $request, Response $response, Exception $exception)
    {
        return $this->cm->exception($request, $response, $exception);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param RouteMatch $routeMatch
     * @param string $controller
     * @return mixed
     */
    public function dispatch(Request $request, Response $response, RouteMatch $routeMatch, $controller)
    {
        return $this->cm->dispatch($request, $response, $routeMatch, $controller);
    }

    /**
     * @param string $controller
     * @return bool
     */
    public function dispatchable($controller)
    {
        return $this->cm->dispatchable($controller);
    }

    /**
     * @param ManagerInterface $cm
     * @return self
     */
    public function setControllerManager(ManagerInterface $cm)
    {
        $this->cm = $cm;
        return $this;
    }
}