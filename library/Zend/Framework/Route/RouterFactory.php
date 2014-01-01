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
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;

class RouterFactory
    extends FactoryListener
{
    /**
     * @param EventInterface $event
     * @return bool|mixed|object
     */
    public function __invoke(EventInterface $event)
    {
        $config = $this->sm->applicationConfig();

        // Defaults
        $routerClass        = 'Zend\Mvc\Router\Http\TreeRouteStack';
        $routerConfig       = isset($config['router']) ? $config['router'] : array();

        // Console environment?
        /*if ($rName === 'ConsoleRouter'                       // force console router
            || ($cName === 'router' && Console::isConsole()) // auto detect console
        ) {
            // We are in a console, use console router defaults.
            $routerClass = 'Zend\Mvc\Router\Console\SimpleRouteStack';
            $routerConfig = isset($config['console']['router']) ? $config['console']['router'] : array();
        }*/

        // Obtain the configured router class, if any
        if (isset($routerConfig['router_class']) && class_exists($routerConfig['router_class'])) {
            $routerClass = $routerConfig['router_class'];
        }

        // Inject the route plugins
        if (!isset($routerConfig['route_plugins'])) {
            $routerConfig['route_plugins'] = $this->sm->routePluginManager();
        }

        // Obtain an instance
        $factory = sprintf('%s::factory', $routerClass);
        return call_user_func($factory, $routerConfig);
    }
}
