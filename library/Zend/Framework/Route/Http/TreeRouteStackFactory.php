<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Route\Manager\ServicesTrait as Route;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class TreeRouteStackFactory
    extends Factory
{
    /**
     *
     */
    use Route;

    /**
     * @param Request $request
     * @param array $options
     * @return TreeRouteStack
     */
    public function __invoke(Request $request, array $options = [])
    {
        $rm = $this->routeManager();

        $router = new TreeRouteStack;

        $router->setRouteManager($rm)
               ->addRoutes($this->config()->router()->routes())
               ->setDefaultParams($this->config()->router()->defaultParams());

        return $router;
    }
}