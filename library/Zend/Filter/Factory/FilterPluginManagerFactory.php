<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Factory;

use Zend\Filter\FilterPluginManager;
use Zend\Filter\StaticFilter;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a filter plugin manager. Custom filters can be added through the "filters_manager"
 * key in the config
 */
class FilterPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['filters_manager']) ? $config['filters_manager'] : array();

        $filterPluginManager = new FilterPluginManager(new Config($config));

        // We inject the global plugin manager inside the StaticFilter so that custom
        // filters can also be used this way
        StaticFilter::setPluginManager($filterPluginManager);

        return $filterPluginManager;
    }
}
