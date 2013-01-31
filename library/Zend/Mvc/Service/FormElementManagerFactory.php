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

class FormElementManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\Form\FormElementManager';

    /**
     * Create and return the MVC controller plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Form\FormElementManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceListener \Zend\ModuleManager\Listener\ServiceListener */
        $serviceListener = $serviceLocator->get('ServiceListener');

        // This will allow to register new form elements easily, either by implementing the FormElementProviderInterface
        // in your Module.php file, or by adding the "form_elements" key in your module.config.php file
        $serviceListener->addServiceManager(
            'FormElementManager',
            'form_elements',
            'Zend\ModuleManager\Feature\FormElementProviderInterface',
            'getFormElementConfig'
        );

        return parent::createService($serviceLocator);
    }
}
