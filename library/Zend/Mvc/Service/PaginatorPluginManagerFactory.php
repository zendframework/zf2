<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class PaginatorPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\Paginator\AdapterPluginManager';

    /**
     * Create and return the MVC controller plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Paginator\AdapterPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);
        return $plugins;
    }
}
