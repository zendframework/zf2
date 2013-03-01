<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

/**
 * Abstract Service Factory.
 *
 * Used to configure multiple loggers.
 */
class LoggerAbstractServiceFactory implements AbstractFactoryInterface
{
    use LoggerServiceFactoryTrait;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        if (isset($config['logger'][$requestedName])) {
            return true;

        } else if (isset($config['logger'][$name])) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return \Zend\Log\Logger
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        if (isset($config['logger'][$requestedName])) {
            return $this->createLogger($config['logger'][$requestedName]);

        } else if (isset($config['logger'][$name])) {
            return $this->createLogger($config['logger'][$name]);

        } else {
            return $this->getDefaultLogger();
        }
    }
}
