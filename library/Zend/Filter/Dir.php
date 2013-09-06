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
 * Filter that returns the directory name of a path
 */
class Dir extends AbstractFilter
{
    /**
     * Returns the directory name of a path
     *
     * {@inheritDoc}
     */
    public function filter($value)
    {
        return dirname((string) $value);
    }
}
