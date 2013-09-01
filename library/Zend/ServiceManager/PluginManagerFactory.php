<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

/**
 * Class PluginManagerFactory
 */
class PluginManagerFactory implements AbstractFactoryInterface
{
    const PLUGIN_MANAGER_CLASS = 'Zend\ServiceManager\AbstractPluginManager';

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceManager|ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $className = array_search($name, $serviceLocator->getCanonicalNames());

        if ($className === false || !class_exists($className)) {
            return false;
        }

        return in_array(static::PLUGIN_MANAGER_CLASS, class_parents($className, true));
    }

    /**
     * Create service with name
     *
     * @param ServiceManager|ServiceLocatorInterface $serviceLocator
     * @param string                                 $name
     * @param string                                 $requestedName
     *
     * @return AbstractPluginManager
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Get the proper class name according to conventions
        $config    = $serviceLocator->get('Config');
        $className = array_search($name, $serviceLocator->getCanonicalNames());

        /** @var \Zend\ServiceManager\AbstractPluginManager $pluginManager */
        $pluginManager = new $className();
        $pluginManager->setServiceLocator($serviceLocator);

        if ($pluginManager instanceof ProvidesConfigKeyPathInterface) {
            $keyPath = $pluginManager->getConfigKeyPath();

            foreach (explode('/', $keyPath) as $p) {
                if (isset($config[$p])) {
                    $config = $config[$p];
                } else {
                    throw new Exception\ServiceNotCreatedException(
                        sprintf('Unable to find the configuration with the key path %s', $keyPath)
                    );
                }
            }

            // Configure the service manager
            (new Config($config))->configureServiceManager($pluginManager);
        }

        return $pluginManager;
    }
}
