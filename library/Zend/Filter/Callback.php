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
 * Filter that uses any valid callable with optional parameters
 */
class Callback extends AbstractFilter
{
    /**
     * @var Callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $callbackParams;

    /**
     * Sets a new callback for this filter
     *
     * @param  Callable $callback
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setCallback(Callable $callback)
    {
        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        }

        $this->callback = $callback;
    }

    /**
     * Returns the set callback
     *
     * @return Callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets parameters for the callback
     *
     * @param  array $params
     * @return Callback
     */
    public function setCallbackParams(array $params)
    {
        $this->callbackParams = $params;
    }

    /**
     * Get parameters for the callback
     *
     * @return mixed
     */
    public function getCallbackParams()
    {
        return $this->callbackParams;
    }

    /**
     * Calls the filter per callback
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $params = $this->callbackParams;

        if (empty($params)) {
            return $this->callback($params);
        }

        array_unshift($params, $value);
        return call_user_func_array($this->callback, $params);
    }
}
