<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Http\Header;

use DateTime;
use DateTimeZone;

/**
 * Expires Header
 *
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21
 */
class Expires extends AbstractDate
{
    /**
     * 
     */
    protected $invalidDate = '';

    /**
     * Get header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Expires';
    }

    /**
     * Checks if the current date is invalid
     *
     * @return boolean
     */
    public function isDateInvalid()
    {
        return ($this->invalidDate === false);
    }

    /**
     * Get the invalid date
     *
     * @return string
     */
    public function getInvalidDate()
    {
        if (!$this->isDateInvalid()) {
            throw new Exception\RuntimeException("The current date isn't invalid");
        }
        return $this->invalidDate;
    }

    /**
     * Set an invalid date string
     * 
     * @param string $invalidDate
     */
    public function setInvalidDate($invalidDate)
    {
        try {
            new DateTime($invalidDate);
        } catch (\Exception $e) {
            $this->invalidDate = $invalidDate;
            return;
        }

        throw new Exception\InvalidArgumentException(
            "The date {$invalidDate} is valid and can't be used as an invalid date"
        );
    }
}
