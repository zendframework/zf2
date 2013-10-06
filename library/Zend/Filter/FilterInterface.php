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
 * Interface that all Zend Framework 3 filters implement
 *
 * NOTE: a filter implements the __invoke magic method, which implies that each filter
 * is a callable and can be called this way: $filter($value)
 */
interface FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value);

    /**
     * Proxy to filter method (this allows to make any filters callable)
     *
     * @param  mixed $value
     * @return mixed
     */
    public function __invoke($value);
}
