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
 * @category  Zend
 * @package   Zend_Measure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Measure;

use Locale;
use NumberFormatter;
use Zend\Math\BigInteger\Adapter\AdapterInterface as BigIntegerAdapter;
use Zend\Math\BigInteger\BigInteger;

/**
 * Abstract class for all measurements
 *
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractMeasure
{
    /**
     * Plain value in standard unit
     *
     * @var string $_value
     */
    protected $_value;

    /**
     * Original type for this unit
     *
     * @var string $_type
     */
    protected $_type;

    /**
     * Locale identifier
     *
     * @var string $_locale
     */
    protected $_locale = null;

    /**
     * Unit types for this measurement
     */
    protected $_units = array();

    /** @var BigIntegerAdapter */
    protected $math;

    /**
     * Zend\Measure\MeasureAbstract is an abstract class for the different measurement types
     *
     * @param  mixed  $value  Value as string, integer, real or float
     * @param  string $type   OPTIONAL a measure type f.e. Zend\Measure\Length::METER
     * @param  string $locale OPTIONAL a BCP 47 compliant language tag
     * @throws Zend\Measure\Exception
     */
    public function __construct($value, $type = null, $locale = null)
    {
        if (($type !== null) && (Locale::getPrimaryLanguage($type) !== null)) {
            $locale = $type;
            $type = null;
        }

        $this->setLocale($locale, true);

        if ($type === null) {
            $type = $this->_units['STANDARD'];
        }

        if (isset($this->_units[$type]) === false) {
            throw new Exception("Type ($type) is unknown");
        }

        $this->setValue($value, $type, $this->_locale);
    }

    /**
     * @param BigIntegerAdapter $math
     */
    public function setMath(BigIntegerAdapter $math)
    {
        $this->math = $math;
    }

    /**
     * If not Adapter is present then create a new one from the BigInteger
     * factory.
     *
     * @return BigIntegerAdapter
     */
    public function getMath()
    {
        if (null === $this->math) {
            $this->math = BigInteger::factory();
        }

        return $this->math;
    }

    /**
     * Returns the actual set locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Sets a new locale for the value representation
     *
     * @param  string  $locale (Optional) A BCP 47 compliant language tag
     * @param  boolean $check  False, check but don't set; True, set the new locale
     * @return Zend\Measure\AbstractMeasure
     */
    public function setLocale($locale = null, $check = false)
    {
        if (null === $locale) {
            $locale = Locale::getPrimaryLanguage(Locale::getDefault());
        } else {
            $locale = Locale::getPrimaryLanguage($locale);
            if (null === $locale) {
                throw new Exception('Language (' . (string)$locale . ') is unknown');
            }
        }

        if (!$check) {
            $this->_locale = (string) $locale;
        }
        return $this;
    }

    /**
     * Returns the internal value
     *
     * @param integer $round  (Optional) Rounds the value to an given precision,
     *                                   Default is -1 which returns without rounding
     * @param  string $locale (Optional) A BCP 47 compliant language tag
     * @return integer|string
     */
    public function getValue($round = -1, $locale = null)
    {
        if ($round < 0) {
            $return = $this->_value;
        } else {
            $return = $this->getMath()->round($this->_value, $round);
        }

        if ($locale !== null) {
            $this->setLocale($locale, true);
            $fmt = new NumberFormatter($locale, NumberFormatter::DECIMAL);
            return $fmt->format($return);
        }

        return $return;
    }

    /**
     * Set a new value
     *
     * @param  integer|string $value   Value as string, integer, real or float
     * @param  string         $type    OPTIONAL A measure type f.e. Zend_Measure_Length::METER
     * @param  string         $locale  OPTIONAL A BCP 47 compliant language tag
     * @throws Zend\Measure\Exception
     * @return Zend\Measure\AbstractMeasure
     */
    public function setValue($value, $type = null, $locale = null)
    {
        if (($type !== null) && (Locale::getPrimaryLanguage($type) !== null)) {
            $locale = $type;
            $type = null;
        }

        if ($locale !== null) {
            $this->setLocale($locale, true);
        }

        if ($type === null) {
            $type = $this->_units['STANDARD'];
        }

        if (empty($this->_units[$type])) {
            throw new Exception("Type ($type) is unknown");
        }

        $fmt = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $value = $fmt->format($value);
        if(intl_is_failure($fmt->getErrorCode())) {
            throw new Exception($fmt->getErrorMessage(), $fmt->getErrorCode());
        }

        $this->_value = $value;
        $this->setType($type);
        return $this;
    }

    /**
     * Returns the original type
     *
     * @return type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set a new type, and convert the value
     *
     * @param  string $type New type to set
     * @throws Zend\Measure\Exception
     * @return Zend\Measure\AbstractMeasure
     */
    public function setType($type)
    {
        if (empty($this->_units[$type])) {
            throw new Exception("Type ($type) is unknown");
        }

        if (empty($this->_type)) {
            $this->_type = $type;
        } else {
            // Convert to standard value
            $value = $this->_value;
            $math = $this->getMath();
            if (is_array($this->_units[$this->getType()][0])) {
                foreach ($this->_units[$this->getType()][0] as $key => $found) {
                    switch ($key) {
                        case "/":
                            if ($found != 0) {
                                $value = $math->div($value, $found, 25);
                            }
                            break;
                        case "+":
                            $value = $math->add($value, $found, 25);
                            break;
                        case "-":
                            $value = $math->sub($value, $found, 25);
                            break;
                        default:
                            $value = $math->mul($value, $found, 25);
                            break;
                    }
                }
            } else {
                $value = $math->mul($value, $this->_units[$this->getType()][0], 25);
            }

            // Convert to expected value
            if (is_array($this->_units[$type][0])) {
                foreach (array_reverse($this->_units[$type][0]) as $key => $found) {
                    switch ($key) {
                        case "/":
                            $value = $math->mul($value, $found, 25);
                            break;
                        case "+":
                            $value = $math->sub($value, $found, 25);
                            break;
                        case "-":
                            $value = $math->add($value, $found, 25);
                            break;
                        default:
                            if ($found != 0) {
                                $value = $math->div($value, $found, 25);
                            }
                            break;
                    }
                }
            } else {
                $value = $math->div($value, $this->_units[$type][0], 25);
            }

            $this->_value = $this->roundToPrecision($value);
            $this->_type  = $type;
        }
        return $this;
    }

    /**
     * Compare if the value and type is equal
     *
     * @param  Zend\Measure\AbstractMeasure $object object to compare
     * @return boolean
     */
    public function equals($object)
    {
        if ((string) $object == $this->toString()) {
            return true;
        }

        return false;
    }

    /**
     * Returns a string representation
     *
     * @param  integer $round  (Optional) Rounds the value to an given exception
     * @param  string  $locale (Optional) A BCP 47 compliant language tag
     * @return string
     */
    public function toString($round = -1, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_locale;
        }

        return $this->getValue($round, $locale) . ' ' . $this->_units[$this->getType()][1];
    }

    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns the conversion list
     *
     * @return array
     */
    public function getConversionList()
    {
        return $this->_units;
    }

    /**
     * Alias function for setType returning the converted unit
     *
     * @param  string  $type   Constant Type
     * @param  integer $round  (Optional) Rounds the value to a given precision
     * @param  string  $locale (Optional) A BCP 47 compliant language tag
     * @return string
     */
    public function convertTo($type, $round = 2, $locale = null)
    {
        $this->setType($type);
        return $this->toString($round, $locale);
    }

    /**
     * Adds an unit to another one
     *
     * @param  Zend\Measure\AbstractMeasure $object object of same unit type
     * @return Zend\Measure\AbstractMeasure
     */
    public function add($object)
    {
        $math = $this->getMath();
        $object->setType($this->getType());
        $value  = $math->add($this->getValue(-1), $object->getValue(-1), 25);

        $this->_value = $this->roundToPrecision($value);
        return $this;
    }

    /**
     * Substracts an unit from another one
     *
     * @param  Zend\Measure\AbstractMeasure $object object of same unit type
     * @return Zend\Measure\AbstractMeasure
     */
    public function sub($object)
    {
        $math = $this->getMath();
        $object->setType($this->getType());
        $value  = $math->sub($this->getValue(-1), $object->getValue(-1), 25);

        $this->_value = $this->roundToPrecision($value);
        return $this;
    }

    /**
     * Compares two units
     *
     * @param  Zend\Measure\AbstractMeasure $object object of same unit type
     * @return boolean
     */
    public function compare($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue(-1) - $object->getValue(-1);

        if ($value < 0) {
            return -1;
        } else if ($value > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Rounds a number to its last significant figure
     *
     * @param integer|float|string $value the number to round
     * @return float the rounded number
     */
    protected function roundToPrecision($value)
    {
        $slength = strlen($value);
        $length  = 0;
        for($i = 1; $i <= $slength; ++$i) {
            if ($value[$slength - $i] != '0') {
                $length = 26 - $i;
                break;
            }
        }

        return $this->getMath()->round($value, $length);
    }
}
