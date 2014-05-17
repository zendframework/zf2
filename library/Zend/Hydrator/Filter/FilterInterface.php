<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Filter;
use Zend\Hydrator\Context\ExtractionContext;

/**
 * An filter is a special class that is run before any property/method is extracted, so that
 * it allows the user to add constraints about what is (or is not) extracted from an object
 */
interface FilterInterface
{
    /**
     * Should return true if it accepts the given property/method, false otherwise
     *
     * @param  string                 $property The name of the property
     * @param  ExtractionContext|null $context Current extraction context
     * @return bool
     */
    public function accept($property, ExtractionContext $context = null);
}
