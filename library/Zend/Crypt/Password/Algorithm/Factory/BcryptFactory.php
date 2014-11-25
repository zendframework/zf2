<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password\Algorithm\Factory;

use Zend\Crypt\Password\Algorithm\Bcrypt;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Service factory that instantiates {@see Bcrypt}.
 */
class BcryptFactory implements FactoryInterface
{
    /**
     * createService(): defined by FactoryInterface.
     *
     * @see    FactoryInterface::createService()
     * @param  ServiceLocatorInterface $serviceLocator
     *
     * @return Bcrypt
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $bcrypt = new Bcrypt();

        if ($serviceLocator instanceof AbstractPluginManager) {
            $config = $serviceLocator->getServiceLocator()->get('Zend\Crypt\Config');
        } else {
            $config = $serviceLocator->get('Zend\Crypt\Config');
        }

        if (isset($config['password']['bcrypt'])) {
            $config = $config['password']['bcrypt'];

            if (isset($config['cost'])) {
                $bcrypt->getBackend()->setCost($config['cost']);
            }
        }

        return $bcrypt;
    }
}
