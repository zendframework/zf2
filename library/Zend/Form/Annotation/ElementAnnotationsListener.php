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
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use Zend\EventManager\EventManagerInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ElementAnnotationsListener extends AbstractAnnotationsListener
{
    /**
     * Attach listeners
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleAllowEmptyAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleAttributesAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleErrorMessageAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleFilterAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleFlagsAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleInputAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleRequiredAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleTypeAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleValidatorAnnotation'));

        $this->listeners[] = $events->attach('discoverName', array($this, 'handleNameAnnotation'));
        $this->listeners[] = $events->attach('discoverName', array($this, 'discoverFallbackName'));
    }

    /**
     * Handle the AllowEmpty annotation
     *
     * Sets the allow_empty flag on the input specification array.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleAllowEmptyAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof AllowEmpty) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['allow_empty'] = true;
    }

    /**
     * Handle the Attributes annotation
     *
     * Sets the attributes array of the element specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleAttributesAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Attributes) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        $elementSpec['spec']['attributes'] = $annotation->getAttributes();
    }

    /**
     * Handle the ErrorMessage annotation
     *
     * Sets the error_message of the input specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleErrorMessageAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof ErrorMessage) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['error_message'] = $annotation->getMessage();
    }

    /**
     * Handle the Filter annotation
     *
     * Adds a filter to the filter chain specification for the input.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleFilterAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Filter) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        if (!isset($inputSpec['filters'])) {
            $inputSpec['filters'] = array();
        }
        $inputSpec['filters'][] = $annotation->getFilter();
    }

    /**
     * Handle the Flags annotation
     *
     * Sets the element flags in the specification (used typically for setting 
     * priority).
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleFlagsAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Flags) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        $elementSpec['flags'] = $annotation->getFlags();
    }

    /**
     * Handle the Input annotation
     *
     * Sets the filter specification for the current element to the specified 
     * input class name.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleInputAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Input) {
            return;
        }

        $name       = $e->getParam('name');
        $filterSpec = $e->getParam('filterSpec');
        $filterSpec[$name] = $annotation->getInput();
    }

    /**
     * Handle the Required annotation
     *
     * Sets the required flag on the input based on the annotation value.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleRequiredAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Required) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['required'] = (bool) $annotation->getRequired();
    }

    /**
     * Handle the Type annotation
     *
     * Sets the element class type to use in the element specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleTypeAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Type) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        $elementSpec['spec']['type'] = $annotation->getType();
    }

    /**
     * Handle the Validator annotation
     *
     * Adds a validator to the validator chain of the input specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleValidatorAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Validator) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = array();
        }
        $inputSpec['validators'][] = $annotation->getValidator();
    }
}
