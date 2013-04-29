<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

interface FormManagerAwareInterface
{
    /**
     * Set <b>Form Manager</b>
     *
     * @param FormManager $formManager
     */
    public function setFormManager(FormManager $formManager);

    /**
     * Get <b>Form Manager</b>
     *
     * @return FormManager
     */
    public function getFormManager();
}
