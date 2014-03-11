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
     * @param RouteMatch $routeMatch
     * @param null $controller
     * @param array $options
     * @return mixed
     */
    public function error(RouteMatch $routeMatch = null, $controller = null, array $options = [])
    {
        return $this->cm->error($routeMatch, $controller, $options);
    }

    /**
     * @param Exception $exception
     * @param array $options
     * @return mixed
     */
    public function exception(Exception $exception, array $options = [])
    {
        return $this->cm->exception($exception, $options);
    }

    /**
     * @param RouteMatch $routeMatch
     * @param string $controller
     * @param array $options
     * @return mixed
     */
    public function dispatch(RouteMatch $routeMatch, $controller, array $options = [])
    {
        return $this->cm->dispatch($routeMatch, $controller, $options);
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