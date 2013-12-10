<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Service;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Storage cache factory for multiple caches.
 */
class StorageCacheAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Configuration key for cache objects
     *
     * @var string
     */
    protected $configKey = 'caches';

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string $name
     * @internal param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name)
    {
        $config = $this->getConfig($serviceLocator);

        if (empty($config)) {
            return false;
        }

        return (isset($config[$name]) && is_array($config[$name]));
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string $name
     * @internal param string $requestedName
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name)
    {
        $config = $this->getConfig($serviceLocator);
        $config = $config[$name];
        return StorageFactory::factory($config);
    }

    /**
     * Retrieve cache configuration, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey])) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
