<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Result\ValidationResult;

/**
 * Validator that assert a value is inferior than a specific value
 *
 * Accepted options are:
 *      - max
 *      - inclusive
 */
class LessThan extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_LESS           = 'notLessThan';
    const NOT_LESS_INCLUSIVE = 'notLessThanInclusive';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_LESS           => "The input is not less than '%max%'",
        self::NOT_LESS_INCLUSIVE => "The input is not less or equal than '%max%'"
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = array('max');

    /**
     * Maximum value
     *
     * @var mixed
     */
    protected $max;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to max
     *
     * If false, then strict comparisons are done, and the value may equal
     * the max option
     *
     * @var bool
     */
    protected $inclusive = false;

    /**
     * Sets the max option
     *
     * @param  mixed $max
     * @return void
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
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
     * Returns true if and only if $value is less than max option, inclusively
     * when the inclusive option is true
     *
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if ($this->inclusive) {
            if ($data > $this->max) {
                return $this->buildErrorValidationResult($data, self::NOT_LESS_INCLUSIVE);
            }
        } else {
            if ($data >= $this->max) {
                return $this->buildErrorValidationResult($data, self::NOT_LESS);
            }
        }

        return new ValidationResult($data);
    }
}
