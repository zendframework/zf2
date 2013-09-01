<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\HelperFactory;

use Zend\Console\Console;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use Zend\View\Helper\Url as UrlHelper;

/**
 * Class UrlFactory
 */
class UrlFactory implements FactoryInterface
{
    /**
     * Create the view helper url
     *
     * @param ServiceLocatorInterface|HelperPluginManager $helperManager
     *
     * @return \Zend\View\Helper\Url
     */
    public function createService(ServiceLocatorInterface $helperManager)
    {
        $sl = $helperManager->getServiceLocator();

        $helper = new UrlHelper();
        $helper->setRouter($sl->get(Console::isConsole() ? 'HttpRouter' : 'Router'));

        /** @var \Zend\Mvc\Router\RouteMatch $routeMatch */
        $routeMatch = $sl->get('application')->getMvcEvent()->getRouteMatch();

        if ($routeMatch instanceof RouteMatch) {
            $helper->setRouteMatch($routeMatch);
        }

        return $helper;
    }
}
