<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Strategy;

use DateTime;
use Zend\Filter\DateTimeFormatter;

class DateTimeFormatterStrategy implements StrategyInterface
{
    /**
     * @var DateTimeFormatter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $format;

    /**
     * Constructor
     *
     * @param string $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * Converts to date time string
     *
     * @param DateTime|string|int
     * @param string
     */
    public function extract($value)
    {
        return $this->getFilter($this->format)->filter($value);
    }

    /**
     * Converts date time string to DateTime instance for injecting to object
     *
     * @param  string|null|int $value
     * @return DateTime|null
     */
    public function hydrate($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return DateTime::createFromFormat($this->format, $value);
    }

    /**
     * Sets filter for formatting date
     *
     * @param  DateTimeFormatter $filter
     * @return self
     */
    public function setFilter(DateTimeFormatter $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Gets filter for formatting date
     *
     * @return DateTimeFormatter
     */
    private function getFilter($format)
    {
        if (!$this->filter) {
            $this->setFilter(new DateTimeFormatter);
        }
        $this->filter->setFormat($format);

        return $this->filter;
    }
}
