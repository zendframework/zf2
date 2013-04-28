<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

trait FormManagerAwareTrait
{
    /**
     * @var FormManager
     */
    protected $formManager;

    /**
     * Set <b>Form Manager</b>
     *
     * @param  FormManager $formManager
     * @return mixed
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
            $this->formManager = new FormManager;
        }
        return $this->formManager;
    }
}
