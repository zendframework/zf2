<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

/**
 * This factory uses <b>Form Manager</b> to grab its dependencies
 */
class FormFactory extends Factory implements FormManagerAwareInterface
{
    /**
     * @var FormManager
     */
    protected $formManager;

    /**
     * @param FormManager $formManager
     */
    public function __construct(FormManager $formManager = null)
    {
        if ($formManager !== null) {
            $this->setFormManager($formManager);
        }
    }

    /**
     * Get <b>Form Element Manager</b>
     *
     * @deprecated Should grab <b>Form Element Manager</b> via <b>Form Manager</b>:
     * <code>$this->getFormManager()->getFormElementManager()</code>
     *
     * @return FormElementManager
     */
    public function getFormElementManager()
    {
        if ($this->formElementManager === null) {
            $this->setFormElementManager($this->getFormManager()->get('FormElementManager'));
        }

        return $this->formElementManager;
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
     * Lazy load a <b>Form Manager</b> if none is set
     *
     * @return FormManager
     */
    public function getFormManager()
    {
        if ($this->formManager === null) {
            $this->setFormManager(new FormManager(new Service\FormManagerConfig));
        }

        return $this->formManager;
    }

    /**
     * Get current <b>Input Filter Factory</b>
     *
     * If none provided, grabs one from <b>Form Manager</b>.
     *
     * @deprecated Should grab <b>Input Filter Factory</b> directly via <b>Form Manager</b>:
     * <code>$this->getFormManager()->getInputFilterFactory()</code>
     *
     * @return InputFilterFactory
     */
    public function getInputFilterFactory()
    {
        if ($this->inputFilterFactory === null) {
            $this->setInputFilterFactory($this->getFormManager()->get('InputFilterFactory'));
        }
        return $this->inputFilterFactory;
    }
}
