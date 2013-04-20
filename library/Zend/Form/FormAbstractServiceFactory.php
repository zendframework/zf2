<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract form factory.
 *
 * Allow create forms via specification defined in config file.
 * Reserved <b>form</b> section.
 */
class FormAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var FormManager
     */
    private $formManager = null;

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        return isset($config['form'][$requestedName]);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ($this->formManager === null && $serviceLocator->has('FormManager')) {
            $this->setFormManager($serviceLocator->get('FormManager'));
        }

        $config = $serviceLocator->get('Config');

        return $this->createForm($config['form'][$requestedName]);
    }

    /**
     * @param  array $spec
     * @return FormInterface
     */
    public function createForm(array $spec = array())
    {
        $formManager = $this->getFormManager();
        $formFactory = $formManager->getFormFactory();
        $form        = $formFactory->createForm($spec);

        if ($form instanceof FormManagerAwareInterface) {
            $form->setFormManager($formManager);
        }

        return $form;
    }

    /**
     * Set <b>Form Manager</b>
     *
     * @param  FormManager $formManager
     * @return self
     */
    public function setFormManager(FormManager $formManager)
    {
        $this->formManager = $formManager;
        return $this;
    }

    /**
     * Get <b>Form Manager</b>
     *
     * @return FormManager
     */
    public function getFormManager()
    {
        if ($this->formManager === null) {
            $this->setFormManager(new FormManager);
        }

        return $this->formManager;
    }
}
