<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

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
     * @param $routeMatch
     * @param $controller
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error($routeMatch, $controller, $request, $response)
    {
        return $this->controllerManager()->error($routeMatch, $controller, $request, $response);
    }

    /**
     * @param $exception
     * @param $request
     * @param $response
     * @return mixed
     */
    public function exception($exception, $request, $response)
    {
        return $this->controllerManager()->exception($exception, $request, $response);
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