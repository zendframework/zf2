<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class FormManager implements ServiceLocatorAwareInterface
{
    /**
     * @var FormElementManager
     */
    protected $formElementManager = null;

    /**
     * @var FormFactory
     */
    protected $formFactory        = null;

    /**
     * @var InputFilterFactory
     */
    protected $inputFilterFactory = null;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator     = null;

    /**
     * Set <b>Form Element Manager</b>
     *
     * @param  FormElementManager $formElementManager
     * @return self
     */
    public function setFormElementManager(FormElementManager $formElementManager)
    {
        $this->formElementManager = $formElementManager;

        return $this;
    }

    /**
     * Get <b>Form Element Manager</b>
     *
     * Lazy loads an instance if none is set.
     *
     * @return FormElementManager
     */
    public function getFormElementManager()
    {
        if ($this->formElementManager === null) {
            $this->setFormElementManager(new FormElementManager());
        }

        return $this->formElementManager;
    }

    /**
     * Set Form Factory
     *
     * @param  Factory $formFactory
     * @return self
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $formFactory->setFormManager($this);
        $this->formFactory = $formFactory;

        return $this;
    }

    /**
     * Get Form Factory
     *
     * Lazy loads an instance if none is set.
     *
     * @return FormFactory
     */
    public function getFormFactory()
    {
        if ($this->formFactory === null) {
            $this->setFormFactory(new FormFactory());
        }

        return $this->formFactory;
    }


    /**
     * Set input filter factory to use when creating forms
     *
     * @param  InputFilterFactory $inputFilterFactory
     * @return Factory
     */
    public function setInputFilterFactory(InputFilterFactory $inputFilterFactory)
    {
        $this->inputFilterFactory = $inputFilterFactory;
        return $this;
    }

    /**
     * Get current input filter factory
     *
     * Lazy loads an instance if none is set.
     *
     * @return InputFilterFactory
     */
    public function getInputFilterFactory()
    {
        if ($this->inputFilterFactory === null) {
            $this->setInputFilterFactory(new InputFilterFactory());
        }
        return $this->inputFilterFactory;
    }

    /**
     * Set service locator
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return self
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
