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
use Zend\Framework\Controller\Config\ConfigInterface;
use Zend\Framework\Controller\ListenerInterface;
use Zend\Mvc\Router\RouteMatch;

interface ManagerInterface
{
    /**
     * @param $name
     * @param null $options
     * @return callable|ListenerInterface
     */
    public function controller($name, $options = null);

    /**
     * @return ConfigInterface
     */
    public function controllers();

    /**
     * @param RouteMatch $routeMatch
     * @param string $controller
     * @param null $options
     * @return mixed
     */
    public function dispatch(RouteMatch $routeMatch, $controller, $options = null);

    /**
     * @param string $controller
     * @return bool
     */
    public function dispatchable($controller);

    /**
     * @param RouteMatch $routeMatch
     * @param null $controller
     * @param null $options
     * @return mixed
     */
    public function error(RouteMatch $routeMatch = null, $controller = null, $options = null);

    /**
     * @param Exception $exception
     * @param null $options
     * @return mixed
     */
    public function exception(Exception $exception, $options = null);

    /**
     * @param RouteMatch $routeMatch
     * @param null $options
     * @return string controller
     */
    public function match(RouteMatch $routeMatch, $options = null);
}
