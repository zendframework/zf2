<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Console\Console;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Mvc\View\Console\ViewManager as ConsoleViewManager;
use Zend\Mvc\View\Http\ViewManager as HttpViewManager;

class ViewManagerFactory implements FactoryInterface
{
    /**
     * Create and return a view manager based on detected environment
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ConsoleViewManager|HttpViewManager
     */
    public function createService(ServiceManager $serviceLocator)
    {
        if (Console::isConsole()) {
            return $serviceLocator->get(new ServiceRequest('ConsoleViewManager'));
        }

        return $serviceLocator->get(new ServiceRequest('HttpViewManager'));
    }

    public function __invoke(ServiceManager $serviceLocator)
    {
        return $this->createService($serviceLocator);
    }
}
