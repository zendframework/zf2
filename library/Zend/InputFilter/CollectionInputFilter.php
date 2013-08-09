<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

/**
 * This is a specialized input filter that can be used to filter an array of same data, using
 * the same input filter
 *
 * @TODO: we should be able to add specific validators that have sense in context of collection,
 *        like min/max array elements, if we allow the collection to have no element... This would
 *        move this logic that is currently done in forms, to the input filter
 */
class CollectionInputFilter extends InputFilter
{
    /**
     * The logic here is to iterate through the data, execute the validation and aggregate
     * the error messages, if any. The collection input filter is considered as invalid if
     * one or more collection element is invalid
     *
     * {@inheritDoc}
     */
    public function isValid($context = null)
    {
        $isValid       = true;
        $errorMessages = array();

        foreach ($this->data as $key => $value) {
            if (!parent::isValid($value)) {
                $errorMessages[$key] = $this->getErrorMessages();
                $isValid             = false;
            }
        }

        $this->setErrorMessages($this->name, $errorMessages);

        return $isValid;
    }
}
