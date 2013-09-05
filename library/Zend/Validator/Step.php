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
 * A validator that validate if a scalar value is in a given step
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 *      - base_value
 *      - step
 */
class Step extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID = 'typeInvalid';
    const NOT_STEP = 'stepInvalid';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid value given. Scalar expected",
        self::NOT_STEP => "The input is not a valid step"
    );

    /**
     * @var float
     */
    protected $baseValue = 0.0;

    /**
     * @var float
     */
    protected $step = 1.0;

    /**
     * Sets the base value from which the step should be computed
     *
     * @param  mixed $baseValue
     * @return void
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = (float) $baseValue;
    }

    /**
     * Returns the base value from which the step should be computed
     *
     * @return float
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step value
     *
     * @param  mixed $step
     * @return void
     */
    public function setStep($step)
    {
        $this->step = (float) $step;
    }

    /**
     * Returns the step value
     *
     * @return float
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_numeric($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        $fmod = $this->fmod($data - $this->baseValue, $this->step);

        if ($fmod !== 0.0 && $fmod !== $this->step) {
            return $this->buildErrorValidationResult($data, self::NOT_STEP);
        }

        return new ValidationResult($data);
    }

    /**
     * Replaces the internal fmod function which give wrong results on many cases
     *
     * @param  float $x
     * @param  float $y
     * @return float
     */
    protected function fmod($x, $y)
    {
        if ($y == 0.0) {
            return 1.0;
        }

        // Find the maximum precision from both input params to give accurate results
        $precision = strlen(substr($x, strpos($x, '.') + 1)) + strlen(substr($y, strpos($y, '.') + 1));

        return round($x - $y * floor($x / $y), $precision);
    }
}
