<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\InputFilter\Result\InputFilterResult;
use Zend\Validator\File\UploadFile as UploadValidator;

/**
 * @TODO: Refactor for ZF3
 *
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
     * @param  bool $autoPrependUploadValidator
     * @return void
     */
    public function setAutoPrependUploadValidator($autoPrependUploadValidator)
    {
        $this->autoPrependUploadValidator = (bool) $autoPrependUploadValidator;
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
    public function runAgainst($value, $context = null)
    {
        $this->injectUploadValidator();

        // Do not run the filters yet for file uploads

        if (!is_array($value)) {
            // This can happen in an AJAX POST, where the input comes across as a string
            $value = [
                'tmp_name' => $value,
                'name'     => $value,
                'size'     => 0,
                'type'     => '',
                'error'    => UPLOAD_ERR_NO_FILE,
            ];
        }

        $isValid = true;

        if (is_array($value) && isset($value['tmp_name'])) {
            // Single file input
            if (!$this->validatorChain->isValid($value, $context)) {
                $isValid = false;
            }
        } elseif (is_array($value) && !empty($value) && isset($value[0]['tmp_name'])) {
            // Multiple file input (multiple attribute set)
            foreach ($value as $singleFile) {
                if (!$this->validatorChain->isValid($singleFile, $context)) {
                    $isValid = false;
                    break; // No need to process the other
                }
            }
        }

        if (!$isValid) {
            return new InputFilterResult($value, null, $this->validatorChain->getMessages());
        }

        return new InputFilterResult($value, $this->getFilteredValue($value));
    }

    /**
     * Filter the value
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function getFilteredValue($value)
    {
        if (is_array($value)) {
            // Run filters after validation, so that is_uploaded_file() validation is not affected by filters.
            $filterChain = $this->getFilterChain();

            if (isset($value['tmp_name'])) {
                // Single file input
                $value = $filterChain->filter($value);
            } else {
                // Multi file input (multiple attribute set)
                $newValue = [];
                foreach ($value as $fileData) {
                    if (is_array($fileData) && isset($fileData['tmp_name'])) {
                        $newValue[] = $filterChain->filter($fileData);
                    }
                }
                $value = $newValue;
            }
        }

        return $value;
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

        // @TODO: once validator is refactored, change that with a high priority
        $chain->prependByName('Zend\Validator\File\UploadFile', [], true);
        $this->autoPrependUploadValidator = false;
    }
}
