<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Form\Element;

/**
 * Submit form element
 *
 * @uses       \Zend\Form\Element\Xhtml
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Submit extends Xhtml
{
    /**
     * Default view helper to use
     * @var string
     */
    public $helper = 'formSubmit';

    /**
     * Constructor
     *
     * @param  string|array|\Zend\Config\Config $spec Element name or configuration
     * @param  string|array|\Zend\Config\Config $options Element value or configuration
     * @return void
     */
    public function __construct($spec, $options = null)
    {
        if (is_string($spec) && ((null !== $options) && is_string($options))) {
            $options = array('label' => $options);
        }

        if (!isset($options['ignore'])) {
            $options['ignore'] = true;
        }

        parent::__construct($spec, $options);
    }

    /**
     * Return label
     *
     * If no label is present, returns the currently set name.
     *
     * If a translator is present, returns the translated label.
     *
     * @return string
     */
    public function getLabel()
    {
        $value = parent::getLabel();

        if (null === $value) {
            $value = $this->getName();
        }

        if (null !== ($translator = $this->getTranslator())) {
            return $translator->translate($value);
        }

        return $value;
    }

    /**
     * Has this submit button been selected?
     *
     * @return bool
     */
    public function isChecked()
    {
        $value = $this->getValue();

        if (empty($value)) {
            return false;
        }
        if ($value != $this->getLabel()) {
            return false;
        }

        return true;
    }

    /**
     * Default decorators
     *
     * Uses only 'Submit' and 'DtDdWrapper' decorators by default.
     *
     * @return \Zend\Form\Element\Submit
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Tooltip')
                 ->addDecorator('ViewHelper')
                 ->addDecorator('DtDdWrapper');
        }
        return $this;
    }
}
