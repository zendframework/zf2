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
 * A composite filter is built as a tree of filters
 */
class CompositeFilter implements FilterInterface
{
    /**
     * Constant to add with "or" / "and" conditition
     */
    const CONDITION_OR  = 1;
    const CONDITION_AND = 2;

    /**
     * The type of this composite filter
     *
     * @var int
     */
    protected $type;

    /**
     * @var array|FilterInterface[]
     */
    protected $filters;

    /**
     * Constructor
     *
     * @param array $filters
     * @param int   $type
     */
    public function __construct(array $filters = [], $type = self::CONDITION_OR)
    {
        $this->filters = $filters;
        $this->type    = $type;
    }

    /**
     * Set the type of the composite filter
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the type of the composite filter (CONDITION_OR or CONDITION_AND)
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add a new filter to the composite filter
     *
     * @param  FilterInterface $filter
     * @return void
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Get filters
     *
     * @return array|FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Remove a filter from the composite filter
     *
     * Note that this method needs to iterate through each filters,
     * which can be expensive if there are a lot
     *
     * @param  FilterInterface $filter
     * @return bool Returns true if the filter has been removed, false otherwise
     */
    public function removeFilter(FilterInterface $filter)
    {
        foreach ($this->filters as $key => $currentFilter) {
            if ($filter === $currentFilter) {
                unset($this->filters[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Clear all the filters
     *
     * @return void
     */
    public function clearFilters()
    {
        $this->filters = [];
    }

    /**
     * {@inheritDoc}
     */
    public function accept($property, ExtractionContext $context = null)
    {
        // If there are no filters attached, we decide to always accept
        if (empty($this->filters)) {
            return true;
        }

        // If condition is OR, only one filter needs to evaluate to true
        if ($this->type === self::CONDITION_OR) {
            foreach ($this->filters as $filter) {
                if ($filter->accept($property, $context) === true) {
                    return true;
                }
            }

            return false;
        } else {
            // If condition is AND, all the filters need to evaluate to true
            foreach ($this->filters as $filter) {
                if ($filter->accept($property, $context) === false) {
                    return false;
                }
            }

            return true;
        }
    }
}
