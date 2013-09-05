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
 * A validator that can validate if a scalar value is between min and max borders
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 *      - min
 *      - max
 *      - inclusive
 */
class Between extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_BETWEEN        = 'notBetween';
    const NOT_BETWEEN_STRICT = 'notBetweenStrict';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_BETWEEN        => "The input is not between '%min%' and '%max%', inclusively",
        self::NOT_BETWEEN_STRICT => "The input is not strictly between '%min%' and '%max%'"
    );

    /**
     * Variables that can get injected
     *
     * @var array
     */
    protected $messageVariables = array('min', 'max');

    /**
     * Minimum border
     *
     * @var int
     */
    protected $min = 0;

    /**
     * Maximum border
     *
     * @var int
     */
    protected $max = PHP_INT_MAX;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min and/or max
     *
     * @var bool
     */
    protected $inclusive = true;

    /**
     * Set the minimum border
     *
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = (int) $min;
    }

    /**
     * Get the minimum border
     *
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set the maximum border
     *
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = (int) $max;
    }

    /**
     * Get the maximum border
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min and/or max
     *
     * @param bool $inclusive
     */
    public function setInclusive($inclusive)
    {
        $this->inclusive = (bool) $inclusive;
    }

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min and/or max
     *
     * @return bool
     */
    public function isInclusive()
    {
        return $this->inclusive;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if ($this->inclusive) {
            if ($this->min > $data || $data > $this->max) {
                return $this->buildErrorValidationResult($data, self::NOT_BETWEEN);
            }
        } else {
            if ($this->min >= $data || $data >= $this->max) {
                return $this->buildErrorValidationResult($data, self::NOT_BETWEEN_STRICT);
            }
        }

        return new ValidationResult($data);
    }
}
