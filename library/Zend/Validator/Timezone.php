<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace Zend\Validator;

use DateTimeZone;
use Traversable;
use Zend\Stdlib\ArrayUtils;

class Timezone extends AbstractValidator
{
    const INVALID                       = 'invalidTimezone';
    const INVALID_TIMEZONE_LOCATION     = 'invalidTimezoneLocation';
    const INVALID_TIMEZONE_ABBREVIATION = 'invalidTimezoneAbbreviation';

    const LOCATION      = 0b01;
    const ABBREVIATION  = 0b10;
    const ALL           = 0b11;

    /**
     * @var array
     */
    protected $constants = array(
        self::LOCATION       => 'location',
        self::ABBREVIATION   => 'abbreviation',
    );

    /**
     * Default value for types; value = 3
     *
     * @var array
    */
    protected $defaultType = array(
        self::LOCATION,
        self::ABBREVIATION,
    );

    /**
     * @var array
    */
    protected $messageTemplates = array(
        self::INVALID                       => 'Invalid timezone given.',
        self::INVALID_TIMEZONE_LOCATION     => 'Invalid timezone location given.',
        self::INVALID_TIMEZONE_ABBREVIATION => 'Invalid timezone abbreviation given.',
    );

    /**
     * Options for this validator
     *
     * @var array
    */
    protected $options = array();

    /**
     * Constructor
     *
     * @param array|Traversable|int $options OPTIONAL
    */
    public function __construct($options = null)
    {
        $this->setType($this->defaultType);

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['type'] = array_shift($options);
            }

            $options = $temp;
        }

        if (is_array($options)) {
            if (!array_key_exists('type', $options)) {
                $detected = 0;
                $found    = false;
                foreach ($options as $option) {
                    if (in_array($option, $this->constants, true)) {
                        $found = true;
                        $detected += array_search($option, $this->constants);
                    }
                }

                if ($found) {
                    $options['type'] = $detected;
                }
            }
        }

        parent::__construct($options);
    }

    /**
     * Returns the set types
     *
     * @return array
     */
    public function getType()
    {
        return $this->options['type'];
    }

    /**
     * @return int
     */
    public function getDefaultType()
    {
        return $this->calculateTypeValue($this->defaultType);
    }

    /**
     * @param array|int|string $type
     * @return int
     */
    protected function calculateTypeValue($type)
    {
        if (is_array($type)) {
            $detected = 0;
            foreach ($type as $value) {
                if (is_int($value)) {
                    $detected |= $value;
                } elseif (in_array($value, $this->constants)) {
                    $detected |= array_search($value, $this->constants);
                }
            }

            $type = $detected;
        } elseif (is_string($type) && in_array($type, $this->constants)) {
            $type = array_search($type, $this->constants);
        }

        return $type;
    }

    /**
     * Set the types
     *
     * @param  int|array $type
     * @throws Exception\InvalidArgumentException
     * @return Timezone
     */
    public function setType($type = null)
    {
        $type = $this->calculateTypeValue($type);

        if (!is_int($type) || ($type < 1) || ($type > self::ALL)) {
            throw new Exception\InvalidArgumentException('Unknown type');
        }

        $this->options['type'] = $type;

        return $this;
    }

    /**
     * Returns true if timezone location or timezone abbreviations is correct.
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if ($value !== null && !is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $type    = $this->getType();
        $this->setValue($value);
        $object  = false;

        // Check in locations and abbreviations
        if (($type & self::LOCATION) && ($type & self::ABBREVIATION)) {
            $abbrs = DateTimeZone::listAbbreviations();
            $locations = DateTimeZone::listIdentifiers();

            if (!array_key_exists($value, $abbrs) && !in_array($value, $locations)) {
                $this->error(self::INVALID);
                return false;
            }
        } else {
            // Check only in locations
            if ($type & self::LOCATION) {
                $locations = DateTimeZone::listIdentifiers();

                if (!in_array($value, $locations)) {
                    $this->error(self::INVALID_TIMEZONE_LOCATION);
                    return false;
                }
            }

            // Check only in abbreviations
            if ($type & self::ABBREVIATION) {
                $abbrs = DateTimeZone::listAbbreviations();

                if (!array_key_exists($value, $abbrs)) {
                    $this->error(self::INVALID_TIMEZONE_ABBREVIATION);
                    return false;
                }
            }
        }

        return true;
    }
}
