<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Console\Console;
use Zend\Mvc\Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\Framework\ServiceManager\ConfigInterface;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\View\Helper as ViewHelper;
use Zend\View\Helper\HelperInterface as ViewHelperInterface;
use Zend\View\Helper\ViewModel;

use Zend\Framework\Service\AbstractPluginManagerFactory;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\Config as ServiceConfig;
use Zend\Framework\View\Plugin\Manager as PluginManager;

class ManagerFactory implements FactoryInterface
{

    protected $defaultHelperMapClasses = array(
        'Zend\Form\View\HelperConfig',
        'Zend\I18n\View\HelperConfig',
        'Zend\Navigation\View\HelperConfig'
    );

    public function createService(ServiceManager $serviceLocator)
    {
        $configuration = $serviceLocator->get(new ServiceRequest('ApplicationConfig'));

        $config = $serviceLocator->get(new ServiceRequest('ViewManager'))->getViewHelpersConfig();

        $plugins = new PluginManager($serviceLocator, new ServiceConfig($config));

        if (isset($configuration['di']) && $serviceLocator->has('Di')) {
            $plugins->addAbstractFactory($serviceLocator->get('DiAbstractServiceFactory'));
        }

        /*foreach ($this->defaultHelperMapClasses as $configClass) {
            if (is_string($configClass) && class_exists($configClass)) {
                $config = new $configClass;

                if (!$config instanceof ConfigInterface) {
                    throw new Exception\RuntimeException(sprintf(
                        'Invalid service manager configuration class provided; received "%s", expected class implementing %s',
                        $configClass,
                        'Zend\ServiceManager\ConfigInterface'
                    ));
                }

                $config($plugins);
            }
        }*/

        // Configure URL view helper with router
        $plugins->addInvokableClass('urlx', function ($sm) use ($serviceLocator) {
            $helper = new ViewHelper\Url;
            $router = Console::isConsole() ? 'HttpRouter' : 'Router';
            $helper->setRouter($serviceLocator->get(new ServiceRequest($router)));

            $match = $serviceLocator->get(new ServiceRequest('Application'))
                                    ->getMvcEvent()
                                    ->getRouteMatch();

            if ($match instanceof RouteMatch) {
                $helper->setRouteMatch($match);
            }

            return $helper;
        });

        $plugins->addInvokableClass('basepath', function ($sm) use ($serviceLocator) {
            $config = $serviceLocator->get(new ServiceRequest('ApplicationConfig'));
            $basePathHelper = new ViewHelper\BasePath;
            if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                $basePathHelper->setBasePath($config['view_manager']['base_path']);
            } else {
                $request = $serviceLocator->get('Request');
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
        $plugins->addInvokableClass('doctype', function ($sm) use ($serviceLocator) {
            $config = $serviceLocator->get(new ServiceRequest('ApplicationConfig'));
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
