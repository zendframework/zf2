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
use Zend\Filter\FilterInterface;

class DateTimeFormatterStrategy implements StrategyInterface
{
    /**
     * @var DateTimeFormatter
     */
    protected $filter;

    /**
     * Constructor
     *
     * @param string|null $format
     */
    public function __construct($format = null)
    {
        if ($format !== null) {
            $this->setFormat($format);
        }
    }

    /**
     * Sets format
     *
     * @param  string $format
     * @return self
     */
    public function setFormat($format)
    {
        $this->getFilter()->setFormat($format);

        return $this;
    }

    /**
     * Converts to date time string
     *
     * @param DateTime|string|int
     * @param string
     */
    public function extract($value)
    {
        return $this->getFilter()->filter($value);
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

        return DateTime::createFromFormat($this->getFilter()->getFormat(), $value);
    }

    /**
     * Sets filter for formatting date
     *
     * @param FilterInterface $filter
     * @return self
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Gets filter for formatting date
     *
     * @return FilterInterface
     */
    public function getFilter()
    {
        if (!$this->filter) {
            $this->setFilter(new DateTimeFormatter);
        }

        return $this->filter;
    }
}
