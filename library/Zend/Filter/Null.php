<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

/**
 * Filter that transforms any value to a null representation based on some criteria
 */
class Null extends AbstractFilter
{
    /**
     * Type constants
     */
    const TYPE_BOOLEAN      = 1;
    const TYPE_INTEGER      = 2;
    const TYPE_EMPTY_ARRAY  = 4;
    const TYPE_STRING       = 8;
    const TYPE_ZERO_STRING  = 16;
    const TYPE_FLOAT        = 32;
    const TYPE_ALL          = 63;

    /**
     * @var int
     */
    protected $type = self::TYPE_ALL;

    /**
     * Set boolean types
     *
     * @param  int|int[] $type
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setType($type)
    {
        if (is_array($type)) {
            $result = 0;

            foreach ($type as $value) {
                $result += (int) $value;
            }

            $type = $result;
        }

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
     * Returns null representation of $value, if value is empty and matches
     * types that should be considered null.
     *
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $type = $this->getType();

        // FLOAT (0.0)
        if ($type >= self::TYPE_FLOAT) {
            $type -= self::TYPE_FLOAT;
            if (is_float($value) && ($value == 0.0)) {
                return null;
            }
        }

        // STRING ZERO ('0')
        if ($type >= self::TYPE_ZERO_STRING) {
            $type -= self::TYPE_ZERO_STRING;
            if (is_string($value) && ($value == '0')) {
                return null;
            }
        }

        // STRING ('')
        if ($type >= self::TYPE_STRING) {
            $type -= self::TYPE_STRING;
            if (is_string($value) && ($value == '')) {
                return null;
            }
        }

        // EMPTY_ARRAY (array())
        if ($type >= self::TYPE_EMPTY_ARRAY) {
            $type -= self::TYPE_EMPTY_ARRAY;
            if (is_array($value) && ($value == array())) {
                return null;
            }
        }

        // INTEGER (0)
        if ($type >= self::TYPE_INTEGER) {
            $type -= self::TYPE_INTEGER;
            if (is_int($value) && ($value == 0)) {
                return null;
            }
        }

        // BOOLEAN (false)
        if ($type >= self::TYPE_BOOLEAN) {
            $type -= self::TYPE_BOOLEAN;
            if (is_bool($value) && ($value == false)) {
                return null;
            }
        }

        return $value;
    }
}
