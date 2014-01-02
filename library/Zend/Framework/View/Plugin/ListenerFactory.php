<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\Mvc\Exception;
use Zend\View\Helper as ViewHelper;

class ListenerFactory
    extends FactoryListener
{
    /**
     * @param EventInterface $event
     * @return Listener
     */
    public function __invoke(EventInterface $event)
    {
        $config = $this->sm->viewManager()->viewHelpers();

        $plugins = new Listener($config, $this->sm);

        // Configure URL view helper with router
        /*$plugins->configure('url', function ($sm) use ($sm) {
            $helper = new ViewHelper\Url;
            $router = Console::isConsole() ? 'HttpRouter' : 'Router';
            $helper->setRouter($sm->getService($router));

            $match = $sm->application()
                        ->mvcEvent()
                        ->routeMatch();

            if ($match instanceof RouteMatch) {
                $helper->setRouteMatch($match);
            }

            return $helper;
        });*/

        $plugins->configure('basepath', function () {
            $config = $this->sm->applicationConfig();
            $basePathHelper = new ViewHelper\BasePath;
            if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                $basePathHelper->setBasePath($config['view_manager']['base_path']);
            } else {
                $request = $this->sm->request();
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
        $plugins->configure('doctype', function () {
            $config = $this->sm->applicationConfig();
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
