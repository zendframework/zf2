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

trait ServicesTrait
{
    /**
     * @return ManagerInterface
     */
    public function controllerManager()
    {
        return $this->sm->get('Controller\Manager');
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
        return $this->controllerManager()->error($request, $response, $routeMatch, $controller);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Exception $exception
     * @return mixed
     */
    public function exception(Request $request, Response $response, Exception $exception)
    {
        return $this->controllerManager()->exception($request, $response, $exception);
    }

    /**
     * @param $controller
     * @param $routeMatch
     * @param $request
     * @param $response
     * @return mixed
     */
    public function dispatch($controller, $routeMatch, $request, $response)
    {
        return $this->controllerManager()->dispatch($controller, $routeMatch, $request, $response);
    }

    /**
     * @param string $controller
     * @return bool
     */
    public function dispatchable($controller)
    {
        return $this->controllerManager()->dispatchable($controller);
    }

    /**
     * @param ManagerInterface $cm
     * @return self
     */
    public function setControllerManager(ManagerInterface $cm)
    {
        $this->sm->add('Controller\Manager', $cm);
        return $this;
    }
}