<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class Digits extends AbstractFilter
{
    /**
     * Returns the string $value, removing all but digit characters
     * {@inheritDoc}
     */
    public function filter($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
