<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Crypt\Password\HandlerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Config;

/**
 * Service factory that instantiates {@see HandlerManager}.
 */
class CryptHandlerManagerFactory implements FactoryInterface
{
    /**
     * Create the {@see Zend\Crypt\Password\HandlerManager}
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return HandlerManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Zend\Crypt\Config');

        if (isset($config['password']['handler_manager'])) {
            $managerConfig = new Config($config['password']['handler_manager']);
        } else {
            $managerConfig = null;
        }

        return new HandlerManager($managerConfig);
    }
}
