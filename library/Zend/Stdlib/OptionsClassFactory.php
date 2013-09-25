<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ProvidesConfigKeyPathInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception;

/**
 * Class OptionsClassFactory
 */
class OptionsClassFactory implements AbstractFactoryInterface
{
    const OPTIONS_CLASS = 'Zend\Stdlib\AbstractOptions';

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $className = array_search($name, $serviceLocator->getCanonicalNames());

        if ($className === false || !class_exists($className)) {
            return false;
        }

        return in_array(static::OPTIONS_CLASS, class_parents($className, true));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException On an invalid configuration key
     *
     * @return AbstractOptions
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $className = array_search($name, $serviceLocator->getCanonicalNames());

        /** @var \Zend\Stdlib\AbstractOptions $options */
        $options = new $className();

        if ($options instanceof ProvidesConfigKeyPathInterface) {
            $config  = $serviceLocator->get('Config');
            $keyPath = $options->getConfigKeyPath();

            foreach (explode('/', $keyPath) as $p) {
                if (isset($config[$p])) {
                    $config = $config[$p];
                } else {
                    throw new Exception\ServiceNotCreatedException(
                        sprintf('Unable to find the configuration with the key path %s', $keyPath)
                    );
                }
            }

            $options->setFromArray($config);
        }

        return $options;
    }
}
