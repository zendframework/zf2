<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Factory;

use Zend\Crypt\Filter\Adapter\EncryptionAdapterPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create an encryption adapter plugin manager. Custom filters can be added through
 * the "encryption_adapter_manager" key in the config
 */
class EncryptionAdapterPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['encryption_adapter_manager']) ? $config['encryption_adapter_manager'] : array();

        return new EncryptionAdapterPluginManager(new Config($config));
    }
}
