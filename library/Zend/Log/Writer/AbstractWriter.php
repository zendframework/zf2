<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log\Writer;

use Zend\Log\Exception;
use Zend\Log\Filter;
use Zend\Log\Formatter\FormatterInterface as Formatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * Filter plugins
     *
     * @var FilterPluginManager
     */
    protected $filterPlugins;

    /**
     * Filter chain
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Formats the log message before writing
     *
     * @var Formatter
     */
    protected $formatter;

    /**
     * Add a filter specific to this writer.
     *
     * @param  int|string|Filter\FilterInterface $filter
     * @return AbstractWriter
     * @throws Exception\InvalidArgumentException
     */
    public function addFilter($filter, array $options = null)
    {
        if (is_int($filter)) {
            $filter = new Filter\Priority($filter);
        }

        if (is_string($filter)) {
            $filter = $this->filterPlugin($filter, $options);
        }

        if (!$filter instanceof Filter\FilterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Writer must implement Zend\Log\Filter\FilterInterface; received "%s"',
                is_object($filter) ? get_class($filter) : gettype($filter)
            ));
        }

        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Get filter plugin manager
     *
     * @return FilterPluginManager
     */
    public function getFilterPluginManager()
    {
        if (null === $this->filterPlugins) {
            $this->setFilterPluginManager(new FilterPluginManager());
        }
        return $this->filterPlugins;
    }

    /**
     * Set filter plugin manager
     *
     * @param  string|FilterPluginManager $plugins
     * @return Logger
     * @throws Exception\InvalidArgumentException
     */
    public function setFilterPluginManager($plugins)
    {
        if (is_string($plugins)) {
            $plugins = new $plugins;
        }
        if (!$plugins instanceof FilterPluginManager) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Writer plugin manager must extend %s\FilterPluginManager; received %s',
                __NAMESPACE__,
                is_object($plugins) ? get_class($plugins) : gettype($plugins)
            ));
        }

        $this->filterPlugins = $plugins;
        return $this;
    }

    /**
     * Get filter instance
     *
     * @param string $name
     * @param array|null $options
     * @return Writer
     */
    public function filterPlugin($name, array $options = null)
    {
        return $this->getFilterPluginManager()->get($name, $options);
    }

    /**
     * Log a message to this writer.
     *
     * @param array $event log data event
     * @return void
     */
    public function write(array $event)
    {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($event)) {
                return;
            }
        }

        // exception occurs on error
        $this->doWrite($event);
    }

    /**
     * Set a new formatter for this writer
     *
     * @param  Formatter $formatter
     * @return self
     */
    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Perform shutdown activities such as closing open resources
     *
     * @return void
     */
    public function shutdown()
    {}

    /**
     * Write a message to the log
     *
     * @param array $event log data event
     * @return void
     */
    abstract protected function doWrite(array $event);
}
