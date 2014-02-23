<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use DateTimeZone;

trigger_error('DateTime extension deprecated as of ZF 2.1.4; use the \DateTime constructor to parse extended ISO8601 dates instead', E_USER_DEPRECATED);

/**
 * DateTime
 *
 * An extension of the \DateTime object.
 *
 * @deprecated
 */
class DateTime extends \DateTime
{
    /**
     * Returns new DateTime object.
     *
     * Works around PHP bug #54340.
     *
     * @link https://bugs.php.net/bug.php?id=54340
     * @param string            $time                 A date/time string. Enter NULL here to obtain the current time
     *                                                when using the $timezone parameter.
     * @param DateTimeZone|null $timezone             A DateTimeZone object representing the timezone of $time. If $timezone
     *                                                is omitted, the current timezone will be used.
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        // Workaround not required for PHP 5.3.7 or newer
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            return parent::__construct($time, $timezone);
        }

        // Check if using relative constructs
        if (!stristr($time, 'last') && !stristr($time, 'first')) {
            if ($timezone) {
                return parent::__construct($time, $timezone);
            } else {
                return parent::__construct($time);
            }
        }

        // Use current time with constructor to prevent setting 'first_last_day_of' flag.
        // The branching is required for older PHP to prevent DateTime exception of null timezone.
        if ($timezone) {
            parent::__construct('now', $timezone);
        } else {
            parent::__construct('now');
        }

        // Set the timestamp by relying on strtotime and avoiding setting the
        // internal 'first_last_day_of' flag of DateTime object.
        $this->setTimestamp(
            strtotime($time, $this->getTimestamp())
        );

        return $this;
    }

    /**
     * Alter the timestamp of a DateTime object by incrementing or decrementing in a format accepted by strtotime().
     *
     * Works around PHP bug #54340.
     *
     * @link   https://bugs.php.net/bug.php?id=54340
     * @param  string $modify  A date/time string.
     * @return DateTime             Returns the DateTime object for method chaining or FALSE on failure.
     */
    public function modify($modify)
    {
        // Workaround not required for PHP 5.3.7 or newer
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            return parent::modify($modify);
        }

        // Check if using relative constructs
        if (!stristr($modify, 'last') && !stristr($modify, 'first')) {
            return parent::modify($modify);
        }

        // Set the timestamp by relying on strtotime and avoiding setting the
        // internal 'first_last_day_of' flag of DateTime object.
        $parsedTimestamp = strtotime($modify, $this->getTimestamp());
        if ($parsedTimestamp === false) {
            return false; // something went wrong parsing the date
        }
        $this->setTimestamp($parsedTimestamp);

        return $this;
    }


    /**
     * The DateTime::ISO8601 constant used by php's native DateTime object does
     * not allow for fractions of a second. This function better handles ISO8601
     * formatted date strings.
     *
     * @param  string       $time
     * @param  DateTimeZone $timezone
     * @return mixed
     */
    public static function createFromISO8601($time, DateTimeZone $timezone = null)
    {
        $format = self::ISO8601;
        if (isset($time[19]) && $time[19] === '.') {
            $format = 'Y-m-d\TH:i:s.uO';
        }

        if ($timezone !== null) {
            return self::createFromFormat($format, $time, $timezone);
        }

        return self::createFromFormat($format, $time);
    }
}
