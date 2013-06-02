<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;

class Boolean extends AbstractFilter
{
    const TYPE_BOOLEAN        = 1;
    const TYPE_INTEGER        = 2;
    const TYPE_FLOAT          = 4;
    const TYPE_STRING         = 8;
    const TYPE_ZERO_STRING    = 16;
    const TYPE_EMPTY_ARRAY    = 32;
    const TYPE_NULL           = 64;
    const TYPE_PHP            = 127;
    const TYPE_FALSE_STRING   = 128;
    const TYPE_LOCALIZED      = 256;
    const TYPE_ALL            = 511;

    /**
     * @var string
     */
    protected $type = self::TYPE_PHP;

    /**
     * @var bool
     */
    protected $casting = true;

    /**
     * @var array
     */
    protected $translations = array();

    /**
     * Set boolean types. You can use bit operators to combine multiple types (eg TYPE_PHP | TYPE_LOCALIZED)
     *
     * @param  int $type
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setType($type)
    {
        if (!is_int($type) || ($type < 0) || ($type > self::TYPE_ALL)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Unknown type value "%s" (%s)',
                $type,
                gettype($type)
            ));
        }

        $this->type = $type;
    }

    /**
     * Returns defined boolean types
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the working mode
     *
     * @param  bool $casting When true this filter works like cast, when false it recognises only true
     *                       and false and all other values are returned as is
     * @return void
     */
    public function setCasting($casting)
    {
        $this->casting = (bool) $casting;
    }

    /**
     * Returns the casting option
     *
     * @return bool
     */
    public function getCasting()
    {
        return $this->casting;
    }

    /**
     * @param  array|Traversable $translations
     * @throws Exception\InvalidArgumentException
     * @return bool
     */
    public function setTranslations($translations)
    {
        if (!is_array($translations) && !$translations instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($translations) ? get_class($translations) : gettype($translations))
            ));
        }

        foreach ($translations as $message => $flag) {
            $this->translations[$message] = (bool) $flag;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Returns a boolean representation of $value
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $type    = $this->getType();
        $casting = $this->getCasting();

        // LOCALIZED
        if ($type >= self::TYPE_LOCALIZED) {
            $type -= self::TYPE_LOCALIZED;
            if (is_string($value)) {
                if (isset($this->translations[$value])) {
                    return (bool) $this->translations[$value];
                }
            }
        }

        // FALSE_STRING ('false')
        if ($type >= self::TYPE_FALSE_STRING) {
            $type -= self::TYPE_FALSE_STRING;
            if (is_string($value) && (strtolower($value) == 'false')) {
                return false;
            }

            if (!$casting && is_string($value) && (strtolower($value) == 'true')) {
                return true;
            }
        }

        // NULL (null)
        if ($type >= self::TYPE_NULL) {
            $type -= self::TYPE_NULL;
            if ($value === null) {
                return false;
            }
        }

        // EMPTY_ARRAY (array())
        if ($type >= self::TYPE_EMPTY_ARRAY) {
            $type -= self::TYPE_EMPTY_ARRAY;
            if (is_array($value) && ($value == array())) {
                return false;
            }
        }

        // ZERO_STRING ('0')
        if ($type >= self::TYPE_ZERO_STRING) {
            $type -= self::TYPE_ZERO_STRING;
            if (is_string($value) && ($value == '0')) {
                return false;
            }

            if (!$casting && (is_string($value)) && ($value == '1')) {
                return true;
            }
        }

        // STRING ('')
        if ($type >= self::TYPE_STRING) {
            $type -= self::TYPE_STRING;
            if (is_string($value) && ($value == '')) {
                return false;
            }
        }

        // FLOAT (0.0)
        if ($type >= self::TYPE_FLOAT) {
            $type -= self::TYPE_FLOAT;
            if (is_float($value) && ($value == 0.0)) {
                return false;
            }

            if (!$casting && is_float($value) && ($value == 1.0)) {
                return true;
            }
        }

        // INTEGER (0)
        if ($type >= self::TYPE_INTEGER) {
            $type -= self::TYPE_INTEGER;
            if (is_int($value) && ($value == 0)) {
                return false;
            }

            if (!$casting && is_int($value) && ($value == 1)) {
                return true;
            }
        }

        // BOOLEAN (false)
        if ($type >= self::TYPE_BOOLEAN) {
            $type -= self::TYPE_BOOLEAN;
            if (is_bool($value)) {
                return $value;
            }
        }

        if ($casting) {
            return true;
        }

        return $value;
    }
}
