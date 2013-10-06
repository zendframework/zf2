<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Factory;

use Zend\Filter\Compress\CompressionAdapterPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a compression adapter plugin manager. Custom filters can be added through the "compression_adapter_manager"
 * key in the config
 */
class CompressionAdapterPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['compression_adapter_manager']) ? $config['compression_adapter_manager'] : array();

        $compressionAdapterPluginManager = new CompressionAdapterPluginManager(new Config($config));

        return $compressionAdapterPluginManager;
    }
}
