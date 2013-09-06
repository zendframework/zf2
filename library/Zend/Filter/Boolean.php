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
 * Filter that converts a value to its boolean representation
 *
 * This filter uses the FILTER_VALIDATE_BOOLEAN built-in filter. You can find
 * the reference about what are considered true or false: http://www.php.net/manual/en/filter.filters.validate.php
 */
class Boolean extends AbstractFilter
{
    /**
     * Returns a boolean representation of $value
     * 
     * {@inheritDoc}
     */
    public function filter($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
