<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator;

use Zend\Hydrator\Context\ExtractionContext;
use Zend\Hydrator\Context\HydrationContext;
use Zend\Hydrator\Filter\CompositeFilter;
use Zend\Hydrator\NamingStrategy\NamingStrategyInterface;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * This abstract hydrator provides a built-in support for filters and strategies. All
 * standards ZF3 hydrators extend this class
 */
abstract class AbstractHydrator implements HydratorInterface
{
    /**
     * @var CompositeFilter
     */
    protected $compositeFilter;

    /**
     * List of strategies, indexed by a property name
     *
     * @var array|StrategyInterface[]
     */
    protected $strategies = [];

    /**
     * @var NamingStrategyInterface
     */
    protected $namingStrategy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->compositeFilter = new CompositeFilter();
        $this->namingStrategy  = new UnderscoreNamingStrategy();
    }

    /**
     * Set a new strategy for a given property
     *
     * @param  string            $name
     * @param  StrategyInterface $strategy
     * @return void
     */
    public function setStrategy($name, StrategyInterface $strategy)
    {
        $this->strategies[$name] = $strategy;
    }

    /**
     * Remove a strategy for a given property
     *
     * @param  string $name
     * @return void
     */
    public function removeStrategy($name)
    {
        unset($this->strategies[$name]);
    }

    /**
     * Get a strategy for a given property (null if none)
     *
     * @param  string $name
     * @return StrategyInterface|null
     */
    public function getStrategy($name)
    {
        if (isset($this->strategies[$name])) {
            return $this->strategies[$name];
        }

        return isset($this->strategies['*']) ? $this->strategies['*'] : null;
    }

    /**
     * Has a given property a strategy attached to it?
     *
     * @param  string $name
     * @return bool True if the given property has a strategy
     */
    public function hasStrategy($name)
    {
        return isset($this->strategies[$name]) || isset($this->strategies['*']);
    }

    /**
     * Get all the strategies
     *
     * @return array|StrategyInterface[]
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * Clear all the strategies
     *
     * @return void
     */
    public function clearStrategies()
    {
        $this->strategies = [];
    }

    /**
     * Set a naming strategy
     *
     * @param  NamingStrategyInterface $namingStrategy
     * @return void
     */
    public function setNamingStrategy(NamingStrategyInterface $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * Get naming strategy
     *
     * @return NamingStrategyInterface
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }

    /**
     * Extract the value using a strategy, if one is set
     *
     * @param  string            $property The name of the property
     * @param  mixed             $value    The value to extract
     * @param  ExtractionContext $context  The context
     * @return mixed
     */
    public function extractValue($property, $value, ExtractionContext $context)
    {
        if ($this->hasStrategy($property)) {
            return $this->getStrategy($property)->extract($value, $context);
        }

        return $value;
    }

    /**
     * Hydrate the value using a strategy, if one is st
     *
     * @param  string           $property The name of the property
     * @param  mixed            $value    The value to hydrate
     * @param  HydrationContext $context  The context
     * @return mixed
     */
    public function hydrateValue($property, $value, HydrationContext $context)
    {
        if ($this->hasStrategy($property)) {
            return $this->getStrategy($property)->hydrate($value, $context);
        }

        return $value;
    }
}
