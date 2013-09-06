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
 * Allow to execute any filter statically
 */
class StaticFilter
{
    /**
     * @var FilterPluginManager
     */
    protected static $filterPluginManager;

    /**
     * Set plugin manager for resolving filter classes
     *
     * @param  FilterPluginManager $filterPluginManager
     * @return void
     */
    public static function setPluginManager(FilterPluginManager $filterPluginManager)
    {
        static::$filterPluginManager = $filterPluginManager;
    }

    /**
     * Get plugin manager for loading filter classes
     *
     * @return FilterPluginManager
     */
    public static function getPluginManager()
    {
        if (null === static::$filterPluginManager) {
            static::setPluginManager(new FilterPluginManager());
        }

        return static::$filterPluginManager;
    }

    /**
     * Returns a value filtered through a specified filter class, without requiring separate
     * instantiation of the filter object.
     *
     * The first argument of this method is a data input value, that you would have filtered.
     * The second argument is a string, which corresponds to the basename of the filter class,
     * relative to the Zend\Filter namespace. This method automatically loads the class,
     * creates an instance, and applies the filter() method to the data input. You can also pass
     * an array of constructor arguments, if they are needed for the filter class.
     *
     * @param  mixed  $value
     * @param  string $name
     * @param  array  $options          OPTIONAL
     * @return mixed
     * @throws Exception\ExceptionInterface
     */
    public static function execute($value, $name, array $options = array())
    {
        $filter = static::getPluginManager()->get($name, $options);

        return $filter->filter($value);
    }
}
