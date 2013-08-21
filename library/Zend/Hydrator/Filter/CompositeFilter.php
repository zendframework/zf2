<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\ExtractorFilter;

use Countable;

class CompositeExtractorFilter implements ExtractorFilterInterface, Countable
{
    /**
     * Constant to add with "or" / "and" conditition
     */
    const CONDITION_OR = 1;
    const CONDITION_AND = 2;

    /**
     * @var array
     */
    protected $orFilters;

    /**
     * @var array
     */
    protected $andFilters;

    /**
     * Should return true to extract the given property, false otherwise
     *
     * @param  string $property The name of the property
     * @return bool
     */
    public function filter($property)
    {
        // TODO: Implement filter() method.
    }

    /**
     * Proxy to filter, to make it callable
     *
     * @param  $property
     * @return bool
     */
    public function __invoke($property)
    {
        return $this->filter($property);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        // TODO: Implement count() method.
    }
}
