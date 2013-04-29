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
        $formManager = new FormManager;

        $formManager->setFormElementManager($serviceLocator->get('FormElementManager'));

        return $formManager;
    }
}
