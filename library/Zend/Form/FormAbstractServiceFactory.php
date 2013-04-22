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
use Zend\Form\Factory;
use Zend\ServiceManager\Config;

/**
 * Abstract form factory.
 *
 * Allow create forms via specification defined in config file.
 * Reserved <b>form</b> section.
 */
class FormAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var \Zend\Form\Factory
     */
    protected $formFactory;

    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    /**
     * @var FormObjectManager
     */
    protected $formObjectManager;

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
        $config = $serviceLocator->get('Config');

        $factory = $this->getFormFactory($serviceLocator);
        $form = $factory->createForm($config['form'][$requestedName]);
        $form->setFormFactory($factory);

        return $form;
    }

    /**
     * @param Factory $formFactory
     */
    public function setFormFactory(Factory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * @return \Zend\Form\Factory
     */
    public function getFormFactory(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->formFactory) {
            $formFactory = new Factory();
            $formFactory->setFormElementManager($this->getFormElementManager($serviceLocator));
            $formFactory->setFormObjectManager($this->getFormObjectManager($serviceLocator));
            $this->setFormFactory($formFactory);
        }
        return $this->formFactory;
    }

    /**
     * @param FormElementManager $formElementManager
     * @return \Zend\Form\FormAbstractServiceFactory
     */
    public function setFormElementManager(FormElementManager $formElementManager)
    {
        $this->formElementManager = $formElementManager;
        return $this;
    }

    /**
     * @return \Zend\Form\FormElementManager
     */
    public function getFormElementManager(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->formElementManager) {
            $this->setFormElementManager($this->createFormElementManager($serviceLocator));
        }
        return $this->formElementManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormElementManager
     */
    public function createFormElementManager(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator->has('Zend\Form\FormElementManager')) {
            $formElementManager = $serviceLocator->get('Zend\Form\FormElementManager');

        } else {
            $config = $this->getConfig($serviceLocator);
            $formElementManager = new FormElementManager(new Config($config['element_manager']));
        }
        return $formElementManager;
    }

    /**
     * @param FormObjectManager $formObjectManager
     * @return \Zend\Form\FormAbstractServiceFactory
     */
    public function setFormObjectManager(FormObjectManager $formObjectManager)
    {
        $this->formObjectManager = $formObjectManager;
        return $this;
    }

    /**
     * @return \Zend\Form\FormObjectManager
     */
    public function getFormObjectManager(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->formObjectManager) {
            $this->setFormObjectManager($this->createFormObjectManager($serviceLocator));
        }
        return $this->formObjectManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormObjectManager
     */
    public function createFormObjectManager(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator->has('Zend\Form\FormObjectManager')) {
            $formObjectManager = $serviceLocator->get('Zend\Form\FormObjectManager');

        } else {
            $config = $this->getConfig($serviceLocator);
            $formObjectManager = new FormObjectManager(new Config($config['object_manager']));
        }
        return $formObjectManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    public function getConfig(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (empty($config['form'])) {
            $config = array('form' => array(
                'element_manager' => array(),
                'object_manager'  => array(),
            ));

        } else {
            $config['form'] = array_merge(array(
                'element_manager' => array(),
                'object_manager'  => array(),
            ), $config['form']);
        }

        return $config['form'];
    }
}
