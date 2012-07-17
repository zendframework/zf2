<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormElement extends BaseAbstractHelper
{
    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof Element\Captcha) {
            $helper = $renderer->plugin('formCaptcha');
            return $helper($element);
        }

        if ($element instanceof Element\Csrf) {
            $helper = $renderer->plugin('formHidden');
            return $helper($element);
        }

        if ($element instanceof Element\Collection) {
            $helper = $renderer->plugin('formCollection');
            return $helper($element);
        }

        $type    = $element->getAttribute('type');
        $options = $element->getAttribute('options');

        if ('checkbox' == $type) {
            $helper = $renderer->plugin('formCheckbox');
            return $helper($element);
        }

        if ('color' == $type) {
            $helper = $renderer->plugin('formColor');
            return $helper($element);
        }

        if ('date' == $type) {
            $helper = $renderer->plugin('formDate');
            return $helper($element);
        }

        if ('datetime' == $type) {
            $helper = $renderer->plugin('formDateTime');
            return $helper($element);
        }

        if ('datetime-local' == $type) {
            $helper = $renderer->plugin('formDateTimeLocal');
            return $helper($element);
        }

        if ('email' == $type) {
            $helper = $renderer->plugin('formEmail');
            return $helper($element);
        }

        if ('file' == $type) {
            $helper = $renderer->plugin('formFile');
            return $helper($element);
        }

        if ('hidden' == $type) {
            $helper = $renderer->plugin('formHidden');
            return $helper($element);
        }

        if ('image' == $type) {
            $helper = $renderer->plugin('formImage');
            return $helper($element);
        }

        if ('month' == $type) {
            $helper = $renderer->plugin('formMonth');
            return $helper($element);
        }

        if ('multi_checkbox' == $type && is_array($options)) {
            $helper = $renderer->plugin('formMultiCheckbox');
            return $helper($element);
        }

        if ('number' == $type) {
            $helper = $renderer->plugin('formNumber');
            return $helper($element);
        }

        if ('password' == $type) {
            $helper = $renderer->plugin('formPassword');
            return $helper($element);
        }

        if ('radio' == $type && is_array($options)) {
            $helper = $renderer->plugin('formRadio');
            return $helper($element);
        }

        if ('range' == $type) {
            $helper = $renderer->plugin('formRange');
            return $helper($element);
        }

        if ('reset' == $type) {
            $helper = $renderer->plugin('formReset');
            return $helper($element);
        }

        if ('search' == $type) {
            $helper = $renderer->plugin('formSearch');
            return $helper($element);
        }

        if ('select' == $type && is_array($options)) {
            $helper = $renderer->plugin('formSelect');
            return $helper($element);
        }

        if ('submit' == $type) {
            $helper = $renderer->plugin('formSubmit');
            return $helper($element);
        }

        if ('tel' == $type) {
            $helper = $renderer->plugin('formTel');
            return $helper($element);
        }

        if ('text' == $type) {
            $helper = $renderer->plugin('formText');
            return $helper($element);
        }

        if ('textarea' == $type) {
            $helper = $renderer->plugin('formTextarea');
            return $helper($element);
        }

        if ('time' == $type) {
            $helper = $renderer->plugin('formTime');
            return $helper($element);
        }

        if ('time' == $type) {
            $helper = $renderer->plugin('formTime');
            return $helper($element);
        }

        if ('url' == $type) {
            $helper = $renderer->plugin('formUrl');
            return $helper($element);
        }

        if ('week' == $type) {
            $helper = $renderer->plugin('formWeek');
            return $helper($element);
        }

        $helper = $renderer->plugin('formInput');
        return $helper($element);
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }
}
