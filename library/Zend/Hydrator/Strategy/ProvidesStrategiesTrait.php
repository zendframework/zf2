<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * This trait adds the ability to use strategies for a hydrator
 */
trait ProvidesStrategiesTrait
{
    /**
     * List of strategies, indexed by a property name
     *
     * @var array|StrategyInterface[]
     */
    protected $strategies = array();

    /**
     * Add a new strategy for a given property
     *
     * @param  string            $name
     * @param  StrategyInterface $strategy
     * @return void
     */
    public function addStrategy($name, StrategyInterface $strategy)
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
        $this->strategies = array();
    }
}
