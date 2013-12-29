<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

//use Zend\Console\Console;
use Zend\Framework\Service\ListenerConfig as ServiceConfig;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManager;
use Zend\Framework\View\Plugin\Manager as PluginManager;
use Zend\Mvc\Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Helper as ViewHelper;

use Zend\Framework\Mvc\Service\ListenerFactoryInterface as FactoryInterface;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return PluginManager
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->getViewManager()->getViewHelpers();

        $plugins = new PluginManager;
        $plugins->setServiceManager($sm)
                ->setConfig(new ServiceConfig($config));

        // Configure URL view helper with router
        /*$plugins->addInvokableClass('url', function ($sm) use ($sm) {
            $helper = new ViewHelper\Url;
            $router = Console::isConsole() ? 'HttpRouter' : 'Router';
            $helper->setRouter($sm->getService($router));

            $match = $sm->getApplication()
                        ->getMvcEvent()
                        ->getRouteMatch();

            if ($match instanceof RouteMatch) {
                $helper->setRouteMatch($match);
            }

            return $helper;
        });*/

        $plugins->addInvokableClass('basepath', function ($sm) use ($sm) {
            $config = $sm->getApplicationConfig();
            $basePathHelper = new ViewHelper\BasePath;
            if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                $basePathHelper->setBasePath($config['view_manager']['base_path']);
            } else {
                $request = $sm->getRequest();
                if (is_callable(array($request, 'getBasePath'))) {
                    $basePathHelper->setBasePath($request->getBasePath());
                }
            }

            return $basePathHelper;
        });

        /**
         * Configure doctype view helper with doctype from configuration, if available.
         *
         * Other view helpers depend on this to decide which spec to generate their tags
         * based on. This is why it must be set early instead of later in the layout phtml.
         */
        $plugins->addInvokableClass('doctype', function ($sm) use ($sm) {
            $config = $sm->getApplicationConfig();
            $config = isset($config['view_manager']) ? $config['view_manager'] : array();
            $doctypeHelper = new ViewHelper\Doctype;
            if (isset($config['doctype']) && $config['doctype']) {
                $doctypeHelper->setDoctype($config['doctype']);
            }
            return $doctypeHelper;
        });

        return $plugins;
    }
}
