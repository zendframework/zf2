<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class ValidatorManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\Validator\ValidatorPluginManager';

    /**
     * Create and return the validator plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Validator\ValidatorPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceListener \Zend\ModuleManager\Listener\ServiceListener */
        $serviceListener = $serviceLocator->get('ServiceListener');

        // This will allow to register new validators easily, either by implementing the ValidatorProviderInterface
        // in your Module.php file, or by adding the "validators" key in your module.config.php file
        $serviceListener->addServiceManager(
            'ValidatorManager',
            'validators',
            'Zend\ModuleManager\Feature\ValidatorProviderInterface',
            'getValidatorConfig'
        );

        return parent::createService($serviceLocator);
    }
}
