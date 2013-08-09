<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\Validator\File\UploadFile as UploadValidator;

/**
 * FileInput is a special input type for handling uploaded files.
 *
 * It differs from Input in a few ways:
 *
 * 1. It expects the raw value to be in the $_FILES array format.
 *
 * 2. The validators are run BEFORE the filters (the opposite behavior of Input).
 *    This is so is_uploaded_file() validation can be run prior to any filters that
 *    may rename/move/modify the file.
 *
 * 3. Instead of adding a NotEmpty validator, it will (by default) automatically add
 *    a Zend\Validator\File\Upload validator.
 */
class FileInput extends Input
{
    /**
     * @var bool
     */
    protected $isValid = false;

    /**
     * @var bool
     */
    protected $autoPrependUploadValidator = true;

    /**
     * Enable/disable automatically prepending an Upload validator
     *
     * @param  bool $value
     * @return void
     */
    public function setAutoPrependUploadValidator($value)
    {
        $this->autoPrependUploadValidator = (bool) $value;
    }

    /**
     * @return bool
     */
    public function getAutoPrependUploadValidator()
    {
        return $this->autoPrependUploadValidator;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        $value = $this->value;

        if ($this->isValid && is_array($value)) {
            // Run filters after validation, so that is_uploaded_file() validation is not affected by filters.
            $filter = $this->getFilterChain();

            if (isset($value['tmp_name'])) {
                // Single file input
                $value = $filter->filter($value);
            } else {
                // Multi file input (multiple attribute set)
                $newValue = array();
                foreach ($value as $fileData) {
                    if (is_array($fileData) && isset($fileData['tmp_name'])) {
                        $newValue[] = $filter->filter($fileData);
                    }
                }
                $value = $newValue;
            }
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($context = null)
    {
        $this->injectUploadValidator();

        // Do not run the filters yet for file uploads (see getValue())
        $validator = $this->getValidatorChain();
        $rawValue  = $this->getRawValue();

        if (!is_array($rawValue)) {
            // This can happen in an AJAX POST, where the input comes across as a string
            $rawValue = array(
                'tmp_name' => $rawValue,
                'name'     => $rawValue,
                'size'     => 0,
                'type'     => '',
                'error'    => UPLOAD_ERR_NO_FILE,
            );
        }

        if (is_array($rawValue) && isset($rawValue['tmp_name'])) {
            // Single file input
            $this->isValid = $validator->isValid($rawValue, $context);
        } elseif (is_array($rawValue) && !empty($rawValue) && isset($rawValue[0]['tmp_name'])) {
            // Multi file input (multiple attribute set)
            $this->isValid = true;
            foreach ($rawValue as $value) {
                if (!$validator->isValid($value, $context)) {
                    $this->isValid = false;
                    break; // Do not continue processing files if validation fails
                }
            }
        }

        return $this->isValid;
    }

    /**
     * @return void
     */
    protected function injectUploadValidator()
    {
        if (!$this->autoPrependUploadValidator) {
            return;
        }

        $chain = $this->getValidatorChain();

        // Check if Upload validator is already first in chain
        $validators = $chain->getValidators();
        if (isset($validators[0]['instance'])
            && $validators[0]['instance'] instanceof UploadValidator
        ) {
            $this->autoPrependUploadValidator = false;
            return;
        }

        $chain->prependByName('fileuploadfile', array(), true);
        $this->autoPrependUploadValidator = false;
    }
}
