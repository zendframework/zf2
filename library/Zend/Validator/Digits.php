<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Zend\Validator\Result\ValidationResult;

/**
 * A validator that allows to check if a value is only made of digits (any number that
 * contains commas or separator will be considered as invalid by this validator)
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 */
class Digits extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_DIGITS   = 'notDigits';
    const STRING_EMPTY = 'digitsStringEmpty';
    const INVALID      = 'digitsInvalid';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_DIGITS => "The input must contain only digits",
        self::INVALID    => "Invalid type given. String, integer or float expected",
    );

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_numeric($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if (!filter_var($data, FILTER_VALIDATE_INT)) {
            return $this->buildErrorValidationResult($data, self::NOT_DIGITS);
        }

        return new ValidationResult($data);
    }
}
