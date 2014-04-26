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

class DateTimeFormaterStrategy implements StrategyInterface
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
        $value = $this->getFilter()->filter($value);
        if ($value === '' || $value === null) {
            return null;
        }

        return new DateTime($value);
    }

    /**
     * @return DateTimeFormatter
     */
    protected function getFilter()
    {
        if (!$this->filter) {
            $this->filter = new DateTimeFormatter();
        }

        return $this->filter;
    }
}
