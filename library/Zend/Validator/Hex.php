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
 * A validator that allows to check if a value is an hexadecimal number
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 */
class Hex extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID = 'hexInvalid';
    const NOT_HEX = 'notHex';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String expected",
        self::NOT_HEX => "The input contains non-hexadecimal characters",
    );

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data) && !is_int($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if (!ctype_xdigit((string) $data)) {
            return $this->buildErrorValidationResult($data, self::NOT_HEX);
        }

        return new ValidationResult($data);
    }
}
