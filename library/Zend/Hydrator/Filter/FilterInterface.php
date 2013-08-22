<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Filter;

/**
 * An filter is a special filter that is run before any property is extracted, so that
 * it allows the user to add constraints about what is (or is not) extracted from an object
 */
interface FilterInterface
{
    /**
     * Should return true to extract the given property, false otherwise
     *
     * @param  string $property The name of the property
     * @return bool
     */
    public function filter($property);
}
