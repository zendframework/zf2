<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use DateTime;
use Traversable;
use Zend\Validator\Result\ValidationResult;

/**
 * A validator that can validate if a date is valid (optionally according a given format)
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 *      - format
 */
class Date extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID      = 'dateInvalid';
    const INVALID_DATE = 'dateInvalidDate';
    const FALSE_FORMAT = 'dateFalseFormat';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID      => "Invalid type given. String, integer, array or DateTime expected",
        self::INVALID_DATE => "The input does not appear to be a valid date",
        self::FALSE_FORMAT => "The input does not fit the date format '%format%'",
    );

    /**
     * Variables that can get injected
     *
     * @var array
     */
    protected $messageVariables = array('format');

    /**
     * Date format to validate against
     *
     * @var string
     */
    protected $format = 'Y-m-d';

    /**
     * Set the format option
     *
     * @param  string $format
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;
    }

    /**
     * Get the format option
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Returns true if $value is a valid date of the format YYYY-MM-DD
     *
     * If $format is set to something else, the date format is checked according to DateTime
     *
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data)
            && !is_array($data)
            && !is_int($data)
            && !($data instanceof DateTime)
        ) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if ($data instanceof DateTime) {
            return new ValidationResult($data);
        }

        if (is_array($data)) {
            $data = implode('-', $data);
        }

        $date = is_int($data)
            ? date_create("@$data") // from timestamp
            : DateTime::createFromFormat($this->format, $data);

        // Invalid dates can show up as warnings (ie. "2007-02-99") and still return
        // a valid DateTime object.
        $errors = DateTime::getLastErrors();

        if ($errors['error_count'] > 0) {
            return $this->buildErrorValidationResult($data, self::FALSE_FORMAT);
        }

        if ($errors['warning_count'] > 0 || $date === false) {
            return $this->buildErrorValidationResult($data, self::INVALID_DATE);
        }

        return new ValidationResult($data);
    }
}
