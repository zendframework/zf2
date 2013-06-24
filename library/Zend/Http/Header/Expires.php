<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Get header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Expires';
    }

    /**
     * {@inheritdoc}
     *
     * Allows to set 0 as a valid expired date
     * and will be parsed as 1097-01-01 00:00 GMT
     *
     * @param string|DateTime $date
     * @return AbstractDate
     * @throws Exception\InvalidArgumentException
     */
    public function setDate($date)
    {
        if ($date === '0') {
            $date = new DateTime('@0', new DateTimeZone('UTC'));
        }
        return parent::setDate($date);
    }
}
