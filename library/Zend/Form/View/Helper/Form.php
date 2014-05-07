<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\View\Helper;

use Zend\Form\Exception\InvalidArgumentException;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;

/**
 * View helper for rendering Form objects
 */
class Form extends AbstractHelper
{
    /**
     * Attributes valid for this tag (form)
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'accept-charset' => true,
        'action'         => true,
        'autocomplete'   => true,
        'enctype'        => true,
        'method'         => true,
        'name'           => true,
        'novalidate'     => true,
        'target'         => true,
    );

    /**
     * View helper used for rendering form rows
     *
     * @var FormRow|callable
     */
    private $formRowHelper;

    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @return Form
     */
    public function __invoke(FormInterface $form = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form);
    }

    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $formRowHelper = $this->getFormRowHelper();
                $formContent .= $formRowHelper($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }

    /**
     * Generate an opening form tag
     *
     * @param  null|FormInterface $form
     * @return string
     */
    public function openTag(FormInterface $form = null)
    {
        $attributes = array(
            'action' => '',
            'method' => 'get',
        );

        if ($form instanceof FormInterface) {
            $formAttributes = $form->getAttributes();
            if (!array_key_exists('id', $formAttributes) && array_key_exists('name', $formAttributes)) {
                $formAttributes['id'] = $formAttributes['name'];
            }
            $attributes = array_merge($attributes, $formAttributes);
        }

        $tag = sprintf('<form %s>', $this->createAttributesString($attributes));

        return $tag;
    }

    /**
     * Generate a closing form tag
     *
     * @return string
     */
    public function closeTag()
    {
        return '</form>';
    }

    /**
     * @param callable|FormRow $formRowHelper
     */
    public function setFormRowHelper($formRowHelper)
    {
        if(!is_callable($formRowHelper)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects a callable; received "%s"',
                __METHOD__,
                (is_object($formRowHelper) ? get_class($formRowHelper) : gettype($formRowHelper))
            ));
        }
        $this->formRowHelper = $formRowHelper;
    }

    /**
     * Returns the assigned form row view helper
     *
     * @return FormRow|callable
     */
    protected function getFormRowHelper()
    {
        if(!$this->formRowHelper) {
            $this->setFormRowHelper($this->getView()->plugin('formRow'));
        }
        return $this->formRowHelper;
    }
}
