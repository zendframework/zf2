<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use DateInterval;
use DateTime;
use DateTimeZone;
use Zend\Validator\Exception;
use Zend\Validator\Result\ValidationResult;

/**
 * A validator that validate if a date value is in a given step
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 *      - base_value
 *      - step
 *      - format
 *      - timezone
 */
class DateStep extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_DATE = 'dateNotDate';
    const NOT_STEP = 'dateStepNotStep';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_DATE => "The input is not a valid date",
        self::NOT_STEP => "The input is not a valid step"
    );

    /**
     * Optional base date value
     *
     * @var string|int|\DateTime
     */
    protected $baseValue = '1970-01-01T00:00:00Z';

    /**
     * Date step interval (defaults to 1 day).
     *
     * Uses the DateInterval specification.
     *
     * @var DateInterval
     */
    protected $step;

    /**
     * Format to use for parsing date strings
     *
     * @var string
     */
    protected $format = DateTime::ISO8601;

    /**
     * Optional timezone to be used when the baseValue
     * and validation values do not contain timezone info
     *
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * Sets the base value from which the step should be computed
     *
     * @param  string|int|DateTime $baseValue
     * @return void
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
    }

    /**
     * Get the base value from which the step should be computed
     *
     * @return string|int|DateTime
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step date interval
     *
     * @param  DateInterval $step
     * @return DateStep
     */
    public function setStep(DateInterval $step)
    {
        $this->step = $step;
    }

    /**
     * Returns the step date interval
     *
     * @return DateInterval
     */
    public function getStep()
    {
        if (null === $this->step) {
            $this->step = new DateInterval('P1D');
        }

        return $this->step;
    }

    /**
     * Sets the timezone option
     *
     * @param  DateTimeZone $timezone
     * @return void
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Returns the timezone option
     *
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        if (null === $this->timezone) {
            $this->timezone = new DateTimeZone(date_default_timezone_get());
        }

        return $this->timezone;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        $baseDate = $this->convertToDateTime($this->getBaseValue());
        $step     = $this->getStep();

        // Parse the date
        try {
            $valueDate = $this->convertToDateTime($data);
        } catch (Exception\InvalidArgumentException $ex) {
            return $this->buildErrorValidationResult($data, self::NOT_DATE);
        }

        // Same date?
        if ($valueDate == $baseDate) {
            return new ValidationResult($data);
        }

        // Optimization for simple intervals.
        // Handle intervals of just one date or time unit.
        $intervalParts = explode('|', $step->format('%y|%m|%d|%h|%i|%s'));
        $partCounts    = array_count_values($intervalParts);

        if (5 === $partCounts["0"]) {
            // Find the unit with the non-zero interval
            $unitKeys      = array('years', 'months', 'days', 'hours', 'minutes', 'seconds');
            $intervalParts = array_combine($unitKeys, $intervalParts);
            $intervalUnit  = null;
            $stepValue     = null;

            foreach ($intervalParts as $key => $value) {
                if (0 != $value) {
                    $intervalUnit = $key;
                    $stepValue    = (int) $value;
                    break;
                }
            }

            // Get absolute time difference
            $timeDiff  = $valueDate->diff($baseDate, true);
            $diffParts = explode('|', $timeDiff->format('%y|%m|%d|%h|%i|%s'));
            $diffParts = array_combine($unitKeys, $diffParts);

            // Check date units
            if (in_array($intervalUnit, array('years', 'months', 'days'))) {
                switch ($intervalUnit) {
                    case 'years':
                        if (   0 == $diffParts['months']  && 0 == $diffParts['days']
                            && 0 == $diffParts['hours']   && 0 == $diffParts['minutes']
                            && 0 == $diffParts['seconds']
                        ) {
                            if (($diffParts['years'] % $stepValue) === 0) {
                                return new ValidationResult($data);
                            }
                        }
                        break;
                    case 'months':
                        if (   0 == $diffParts['days']    && 0 == $diffParts['hours']
                            && 0 == $diffParts['minutes'] && 0 == $diffParts['seconds']
                        ) {
                            $months = ($diffParts['years'] * 12) + $diffParts['months'];
                            if (($months % $stepValue) === 0) {
                                return new ValidationResult($data);
                            }
                        }
                        break;
                    case 'days':
                        if (   0 == $diffParts['hours'] && 0 == $diffParts['minutes']
                            && 0 == $diffParts['seconds']
                        ) {
                            $days = $timeDiff->format('%a'); // Total days
                            if (($days % $stepValue) === 0) {
                                return new ValidationResult($data);
                            }
                        }
                        break;
                }

                return $this->buildErrorValidationResult($data, self::NOT_STEP);
            }

            // Check time units
            if (in_array($intervalUnit, array('hours', 'minutes', 'seconds'))) {
                // Simple test if $stepValue is 1.
                if (1 == $stepValue) {
                    if ('hours' === $intervalUnit
                        && 0 == $diffParts['minutes'] && 0 == $diffParts['seconds']
                    ) {
                        return new ValidationResult($data);
                    } elseif ('minutes' === $intervalUnit && 0 == $diffParts['seconds']) {
                        return new ValidationResult($data);
                    } elseif ('seconds' === $intervalUnit) {
                        return new ValidationResult($data);
                    }
                }

                // Simple test for same day, when using default baseDate
                if ($baseDate->format('Y-m-d') == $valueDate->format('Y-m-d')
                    && $baseDate->format('Y-m-d') == '1970-01-01'
                ) {
                    switch ($intervalUnit) {
                        case 'hours':
                            if (0 == $diffParts['minutes'] && 0 == $diffParts['seconds']) {
                                if (($diffParts['hours'] % $stepValue) === 0) {
                                    return new ValidationResult($data);
                                }
                            }
                            break;
                        case 'minutes':
                            if (0 == $diffParts['seconds']) {
                                $minutes = ($diffParts['hours'] * 60) + $diffParts['minutes'];
                                if (($minutes % $stepValue) === 0) {
                                    return new ValidationResult($data);
                                }
                            }
                            break;
                        case 'seconds':
                            $seconds = ($diffParts['hours'] * 60)
                                       + ($diffParts['minutes'] * 60)
                                       + $diffParts['seconds'];
                            if (($seconds % $stepValue) === 0) {
                                return new ValidationResult($data);
                            }
                            break;
                    }

                    return $this->buildErrorValidationResult($data, self::NOT_STEP);
                }
            }
        }

        // Fall back to slower (but accurate) method for complex intervals.
        // Keep adding steps to the base date until a match is found
        // or until the value is exceeded.
        if ($baseDate < $valueDate) {
            while ($baseDate < $valueDate) {
                $baseDate->add($step);
                if ($baseDate == $valueDate) {
                    return new ValidationResult($data);
                }
            }
        } else {
            while ($baseDate > $valueDate) {
                $baseDate->sub($step);
                if ($baseDate == $valueDate) {
                    return new ValidationResult($data);
                }
            }
        }

        return $this->buildErrorValidationResult($data, self::NOT_STEP);
    }

    /**
     * Converts an int or string to a DateTime object
     *
     * @param  string|int|DateTime $param
     * @return DateTime
     * @throws Exception\InvalidArgumentException
     */
    protected function convertToDateTime($param)
    {
        $dateObj = $param;

        if (is_int($param)) {
            // Convert from timestamp
            $dateObj = date_create("@$param");
        } elseif (is_string($param)) {
            // Custom week format support
            if (strpos($this->getFormat(), 'Y-\WW') === 0
                && preg_match('/^([0-9]{4})\-W([0-9]{2})/', $param, $matches)
            ) {
                $dateObj = new DateTime();
                $dateObj->setISODate($matches[1], $matches[2]);
            } else {
                $dateObj = DateTime::createFromFormat(
                    $this->getFormat(), $param, $this->getTimezone()
                );
            }
        }

        if (!($dateObj instanceof DateTime)) {
            throw new Exception\InvalidArgumentException('Invalid date param given');
        }

        return $dateObj;
    }
}
