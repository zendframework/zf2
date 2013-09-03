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
     * Filter plugin manager that is used to add filters by name
     *
     * @var FilterPluginManager
     */
    protected $filterPluginManager;

    /**
     * Filter chain
     *
     * @var PriorityQueue|FilterInterface[]
     */
    protected $filters;

    /**
     * Constructor
     */
    public function __construct(FilterPluginManager $filterPluginManager)
    {
        $this->filterPluginManager = $filterPluginManager;
        $this->filters             = new PriorityQueue();
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
     * @param  FilterInterface|Callable $filter A Filter implementation or valid PHP callback
     * @param  int                      $priority Priority at which to enqueue filter; defaults to 1 (higher executes earlier)
     * @return void
     */
    public function attach(Callable $filter, $priority = self::DEFAULT_PRIORITY)
    {
        $this->filters->insert($filter, $priority);
    }

    /**
     * Remove a filter from the chain
     *
     * Note that this method needs to iterate through all the filters, so it can be slow
     *
     * @param  FilterInterface|Callable $filter
     * @return bool True if the filter was successfully removed, false otherwise
     */
    public function remove(Callable $filter)
    {
        foreach ($this->filters as $key => $value) {
            if ($filter === $value) {
                unset($this->filters[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Attach a filter to the chain by its name (using the filter plugin manager)
     *
     * @param  string $name Valid name
     * @param  int    $priority Priority at which to enqueue filter; defaults to 1 (higher executes earlier)
     * @return void
     */
    public function attachByName($name, $priority = self::DEFAULT_PRIORITY)
    {
        $filter = $this->filterPluginManager->get($name);
        $this->filters->insert($filter, $priority);
    }

    /**
     * Remove a filter from the chain by its name
     *
     * Note that this method needs to get the FQCN from the name and iterate through all filters. This
     * can be really slow if filter chain contains a lot of filters
     *
     * @param  string $name
     * @return bool True if the filter was successfully removed, false otherwise
     */
    public function removeByName($name)
    {
        $className = array_search($name, $this->filterPluginManager->getCanonicalNames());

        if ($className === false) {
            return false;
        }

        foreach ($this->filters as $key => $value) {
            if ('\\' . $className instanceof $value) {
                unset($this->filters[$key]);
                return true;
            }
        }

        return false;
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
     * @return PriorityQueue|FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Returns $value filtered through each filter in the chain. Filters are run according to priority
     *
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // @TODO: why do we need to clone?
        $chain = clone $this->filters;

        $filteredValue = $value;
        foreach ($chain as $filter) {
            $filteredValue = $filter($filteredValue);
        }

        return $filteredValue;
    }

    /**
     * Clone filters
     */
    public function __clone()
    {
        $this->filters = clone $this->filters;
    }
}
