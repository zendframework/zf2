<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Controller\ListenerInterface;
use Zend\Framework\Service\ServiceTrait as Service;
use Zend\Mvc\Router\RouteMatch;

class Manager
    implements ManagerInterface
{
    /**
     *
     */
    use Service;

    /**
     * @param RouteMatch $routeMatch
     * @return false|ListenerInterface
     */
    public function controller(RouteMatch $routeMatch)
    {
        return $this->get($routeMatch->getParam('controller'), [$routeMatch]);
    }

    /**
     * @param $name
     * @param array $options
     * @return array|object
     */
    public function get($name, array $options = [])
    {
        return $this->sm->get($name, $options);
    }
}
