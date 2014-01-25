<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;

class TreeRouteStackFactory
    extends FactoryListener
{
    /**
     *
     */
    use Route;

    /**
     * @param EventInterface $event
     * @return TreeRouteStack
     */
    public function service(EventInterface $event)
    {
        $rm = $this->routeManager();

        $router = new TreeRouteStack;

        $router->setRouteManager($rm)
               ->addRoutes($rm->routes())
               ->setDefaultParams($rm->params());

        return $router;
    }
}
