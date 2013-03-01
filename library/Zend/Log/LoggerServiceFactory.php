<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Logger service factory.
 *
 * Used to configure single logger.
 */
class LoggerServiceFactory implements FactoryInterface
{
    use LoggerServiceFactoryTrait;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Zend\Log\Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (isset($config['logger'])) {
            return $this->createLogger($config['logger']);

        } else {
            return $this->getDefaultLogger();
        }
    }
}
