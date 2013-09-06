<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use DateTime;

/**
 * Filter that formats a date (either string, int or DateTime) to a given format
 */
class DateTimeFormatter extends AbstractFilter
{
    /**
     * A valid format string accepted by date()
     *
     * @var string
     */
    protected $format = DateTime::ISO8601;

    /**
     * Set the format string accepted by date() to use when formatting a string
     *
     * @param  string $format
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;
    }

    /**
     * Filter a datetime string by normalizing it to the filters specified format
     * {@inheritDoc}
     */
    public function filter($value)
    {
        try {
            $result = $this->normalizeDateTime($value);
        } catch (\Exception $e) {
            // DateTime threw an exception, an invalid date string was provided
            throw new Exception\InvalidArgumentException('Invalid date string provided', $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * Normalize the provided value to a formatted string
     *
     * @param  string|int|DateTime $value
     * @return string
     */
    protected function normalizeDateTime($value)
    {
        if ($value === '' || $value === null) {
            return $value;
        } elseif (is_int($value)) {
            $value = new DateTime('@' . $value);
        } elseif (!$value instanceof DateTime) {
            $value = new DateTime($value);
        }

        return $value->format($this->format);
    }
}
