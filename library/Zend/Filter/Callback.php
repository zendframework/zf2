<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Callback extends AbstractFilter
{
    /**
     * @var array
     */
    protected $options = array(
        'callback' => null,
        'params'   => null
    );

    /**
     * @param array|Traversable $options
     */
    public function __construct($options)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (!is_array($options)) {
            $args     = func_get_args();
            if (isset($args[0])) {
                $callback = $args[0];
                $params   = null;
                if (isset($args[1])) {
                    $params = $args[1];
                }
                $this->setCallback($callback, $params);
            }
        } else {
            $this->setOptions($options);
        }
    }

    /**
     * Sets a new callback for this filter
     *
     * @param  callable $callback
     * @return Callback
     */
    public function setCallback($callback, $params = null)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter for callback: must be callable'
            );
        }

        $this->options['callback'] = $callback;
        $this->options['params']   = $params;
        return $this;
    }

    /**
     * Returns the set callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->options['callback'];
    }

    /**
     * Calls the filter per callback
     *
     * @param  mixed $value Options for the set callback
     * @return mixed Result from the filter which was callbacked
     */
    public function filter($value)
    {
        $params = array($value);
        if (isset($this->options['params'])) {
            if (!is_array($this->options['params'])) {
                array_push($params, $this->options['params']);
            } else {
                $params = $params + $this->options['params'];
            }
        }

        return call_user_func_array($this->options['callback'], $params);
    }
}
