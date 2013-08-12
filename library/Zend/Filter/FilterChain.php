<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Countable;
use Zend\Stdlib\PriorityQueue;

class FilterChain extends AbstractFilter implements Countable
{
    /**
     * Default priority at which filters are added
     */
    const DEFAULT_PRIORITY = 1;

    /**
     * Filter chain
     *
     * @var PriorityQueue|FilterInterface[]
     */
    protected $filters;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filters = new PriorityQueue();
    }

    /**
     * Return the count of attached filters
     *
     * @return int
     */
    public function count()
    {
        return count($this->filters);
    }

    /**
     * Attach a filter to the chain
     *
     * @param  FilterInterface $filter A Filter implementation or valid PHP callback
     * @param  int             $priority Priority at which to enqueue filter; defaults to 1 (higher executes earlier)
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function attach(FilterInterface $filter, $priority = self::DEFAULT_PRIORITY)
    {
        $this->filters->insert($filter, $priority);
    }

    /**
     * Merge the filter chain with the one given in parameter
     *
     * @param  FilterChain $filterChain
     * @return void
     */
    public function merge(FilterChain $filterChain)
    {
        foreach ($filterChain->filters->toArray(PriorityQueue::EXTR_BOTH) as $item) {
            $this->attach($item['data'], $item['priority']);
        }
    }

    /**
     * Get all the filters
     *
     * @return PriorityQueue
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Returns $value filtered through each filter in the chain. Filters are run according to priority
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $chain = clone $this->filters;

        $valueFiltered = $value;
        foreach ($chain as $filter) {
            $valueFiltered = call_user_func($filter, $valueFiltered);
        }

        return $valueFiltered;
    }

    /**
     * Clone filters
     */
    public function __clone()
    {
        $this->filters = clone $this->filters;
    }
}
