<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator;

/**
 * @uses       \Zend\Filter\Digits
 * @uses       \Zend\Validator\AbstractValidator
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ccnum extends AbstractValidator
{
    /**
     * Validation failure message key for when the value is not of valid length
     */
    const LENGTH   = 'ccnumLength';

    /**
     * Validation failure message key for when the value fails the mod-10 checksum
     */
    const CHECKSUM = 'ccnumChecksum';

    /**
     * Digits filter for input
     *
     * @var \Zend\Filter\Digits
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::LENGTH   => "'%value%' must contain between 13 and 19 digits",
        self::CHECKSUM => "Luhn algorithm (mod-10 checksum) failed on '%value%'"
    );

    public function __construct()
    {
        trigger_error('Using the Ccnum validator is deprecated in favor of the CreditCard validator');
    }

    /**
     * Returns true if and only if $value follows the Luhn algorithm (mod-10 checksum)
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if (null === self::$_filter) {
            self::$_filter = new \Zend\Filter\Digits();
        }

        $valueFiltered = self::$_filter->__invoke($value);

        $length = strlen($valueFiltered);

        if ($length < 13 || $length > 19) {
            $this->_error(self::LENGTH);
            return false;
        }

        $sum    = 0;
        $weight = 2;

        for ($i = $length - 2; $i >= 0; $i--) {
            $digit = $weight * $valueFiltered[$i];
            $sum += floor($digit / 10) + $digit % 10;
            $weight = $weight % 2 + 1;
        }

        if ((10 - $sum % 10) % 10 != $valueFiltered[$length - 1]) {
            $this->_error(self::CHECKSUM, $valueFiltered);
            return false;
        }

        return true;
    }
}
