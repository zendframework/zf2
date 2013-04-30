<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Form\FormManager;
use Zend\Form\Service\FormManagerConfig;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormManagerFactory implements FactoryInterface
{

    /**
     * Create and return the <b>Form Manager</b>
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return FormManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config     = $serviceLocator->get('Config');
        $fmConfig   = isset($config['form_manager']) ? $config['form_manager'] : array();
        $fmServices = isset($fmConfig['services'])   ? $fmConfig['services']   : array();

        $formManager = new FormManager(new FormManagerConfig($fmServices));
        $formManager->setService('FormConfig', $fmConfig);
        $formManager->addPeeringServiceManager($serviceLocator);

        return $formManager;
    }
}
