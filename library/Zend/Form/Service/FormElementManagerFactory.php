<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\Service;

use Zend\Form\FormElementManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormElementManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return FormElementManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $formConfig = $serviceLocator->get('FormConfig');
        $femConfig  = isset($formConfig['elements']) ? $formConfig['elements'] : array();

        $plugins    = new FormElementManager(new Config($femConfig));
        $plugins->setServiceLocator($serviceLocator);

        // Application wide configuration
        $config     = $serviceLocator->get('Config');

        if (isset($config['di']) && $serviceLocator->has('Di')) {
            $plugins->addAbstractFactory($serviceLocator->get('DiAbstractServiceFactory'));
        }

        return $plugins;
    }
}
