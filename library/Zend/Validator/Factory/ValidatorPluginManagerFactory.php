<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator\Factory;

use Zend\Validator\ValidatorPluginManager;
use Zend\Validator\StaticValidator;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a validator plugin manager. Custom filters can be added through the "validator_manager"
 * key in the config
 */
class ValidatorPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['validator_manager']) ? $config['validator_manager'] : array();

        $validatorPluginManager = new ValidatorPluginManager(new Config($config));

        // We inject the global plugin manager inside the StaticValidator so that custom
        // filters can also be used this way
        StaticValidator::setPluginManager($validatorPluginManager);

        return $validatorPluginManager;
    }
}
