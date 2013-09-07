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

/**
 * A filter chain is a specific filter that allows to execute multiple filters one after the other
 *
 * All the filters are saved inside a priority queue, which allows them to be called in a
 * specific ordered
 */
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
     * Attach a filter to the chain by its name (using the filter plugin manager)
     *
     * @param  string $name Valid name
     * @param  array  $options Options to pass to the filter
     * @param  int    $priority Priority at which to enqueue filter; defaults to 1 (higher executes earlier)
     * @return void
     */
    public function attachByName($name, array $options = array(), $priority = self::DEFAULT_PRIORITY)
    {
        // @TODO: this may not work yet because the SM does not support options

        $filter = $this->filterPluginManager->get($name, $options);
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
     * Add filters to the filter chain using concrete instances or specification
     * 
     * @param array $filters
     * @return void
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                $this->attach($filter);
            } elseif (is_array($filter)) {
                $options  = isset($filter['options']) ? $filter['options'] : array();
                $priority = isset($filter['priority']) ? $filter['priority'] : self::DEFAULT_PRIORITY;

                $this->attachByName($filter['name'], $options, $priority);
            }
        }
    }

    /**
     * Set filters using concrete instances or specification
     *
     * @param array|FilterInterface[] $filters
     * @return void
     */
    public function setFilters(array $filters)
    {
        $this->filters = new PriorityQueue();
        $this->addFilters($filters);
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
        foreach ($this->filters as $filter) {
            $value = $filter($value);
        }

        return $value;
    }

    /**
     * Clone filters
     */
    public function __clone()
    {
        $this->filters = clone $this->filters;
    }
}
