<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Console\Console;
use Zend\Framework\Mvc\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManager;

class RouterFactory
    implements FactoryInterface
{
    /**
     * Create and return the router
     *
     * Retrieves the "router" key of the Config service, and uses it
     * to instantiate the router. Uses the TreeRouteStack implementation by
     * default.
     *
     * @param  ServiceManager                  $sm
     * @param  string|null                     $cName
     * @param  string|null                     $rName
     * @return \Zend\Mvc\Router\RouteStackInterface
     */
    public function createService(ServiceManager $sm, $cName = null, $rName = null)
    {
        $config = $sm->getApplicationConfig();

        // Defaults
        $routerClass        = 'Zend\Mvc\Router\Http\TreeRouteStack';
        $routerConfig       = isset($config['router']) ? $config['router'] : array();

        // Console environment?
        if ($rName === 'ConsoleRouter'                       // force console router
            || ($cName === 'router' && Console::isConsole()) // auto detect console
        ) {
            // We are in a console, use console router defaults.
            $routerClass = 'Zend\Mvc\Router\Console\SimpleRouteStack';
            $routerConfig = isset($config['console']['router']) ? $config['console']['router'] : array();
        }

        // Obtain the configured router class, if any
        if (isset($routerConfig['router_class']) && class_exists($routerConfig['router_class'])) {
            $routerClass = $routerConfig['router_class'];
        }

        // Inject the route plugins
        if (!isset($routerConfig['route_plugins'])) {
            $routerConfig['route_plugins'] = $sm->getRoutePluginManager();
        }

        // Obtain an instance
        $factory = sprintf('%s::factory', $routerClass);
        return call_user_func($factory, $routerConfig);
    }
}
