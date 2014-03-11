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

interface ManagerInterface
{
    /**
     * @param RouteMatch $routeMatch
     * @param string $controller
     * @param array $options
     * @return mixed
     */
    public function dispatch(RouteMatch $routeMatch, $controller, array $options = []);

    /**
     * @param string $controller
     * @return bool
     */
    public function dispatchable($controller);

    /**
     * @param RouteMatch $routeMatch
     * @param null $controller
     * @param array $options
     * @return mixed
     */
    public function error(RouteMatch $routeMatch = null, $controller = null, array $options = []);

    /**
     * @param Exception $exception
     * @param array $options
     * @return mixed
     */
    public function exception(Exception $exception, array $options = []);
}
