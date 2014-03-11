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
     * @param RouteMatch $routeMatch
     * @param null $controller
     * @param array $options
     * @return mixed
     */
    public function error(RouteMatch $routeMatch = null, $controller = null, array $options = [])
    {
        return $this->controllerManager()->error($routeMatch, $controller, $options);
    }

    /**
     * @param Exception $exception
     * @param array $options
     * @return mixed
     */
    public function exception(Exception $exception, array $options = [])
    {
        return $this->controllerManager()->exception($exception, $options);
    }

    /**
     * @param RouteMatch $routeMatch
     * @param string $controller
     * @param array $options
     * @return mixed
     */
    public function dispatch(RouteMatch $routeMatch, $controller, array $options = [])
    {
        return $this->controllerManager()->dispatch($routeMatch, $controller, $options);
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