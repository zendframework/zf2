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
 * Validator that assert a value is superior than a specific value
 *
 * Accepted options are:
 *      - min
 *      - inclusive
 */
class GreaterThan extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_GREATER           = 'notGreaterThan';
    const NOT_GREATER_INCLUSIVE = 'notGreaterThanInclusive';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_GREATER           => "The input is not greater than '%min%'",
        self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than '%min%'"
    );

    /**
     * Variables that can get injected
     *
     * @var array
     */
    protected $messageVariables = array('min');

    /**
     * Minimum value
     *
     * @var mixed
     */
    protected $min;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to max
     *
     * If false, then strict comparisons are done, and the value may equal
     * the min option
     *
     * @var bool
     */
    protected $inclusive = false;

    /**
     * Sets the min option
     *
     * @param  mixed $min
     * @return void
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Sets the inclusive option
     *
     * @param  bool $inclusive
     * @return void
     */
    public function setInclusive($inclusive)
    {
        $this->inclusive = (bool) $inclusive;
    }

    /**
     * Returns the inclusive option
     *
     * @return bool
     */
    public function getInclusive()
    {
        return $this->inclusive;
    }

    /**
     * Returns true if and only if $value is greater than min option
     *
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if ($this->inclusive) {
            if ($this->min > $data) {
                return $this->buildErrorValidationResult($data, self::NOT_GREATER_INCLUSIVE);
            }
        } else {
            if ($this->min >= $data) {
                return $this->buildErrorValidationResult($data, self::NOT_GREATER);
            }
        }

        return new ValidationResult($data);
    }
}
