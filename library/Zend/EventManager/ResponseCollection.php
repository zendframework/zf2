<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Collection of signal handler return values
 */
final class ResponseCollection implements Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $responses = [];

    /**
     * @param array $responses
     */
    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    /**
     * Convenient access to the first handler return value.
     *
     * If the collection is empty, returns null. Otherwise, returns value
     * returned by first handler.
     *
     * @return mixed The first handler return value
     */
    public function first()
    {
        if (empty($this->responses)) {
            return null;
        }

        reset($this->responses);
        return current($this->responses);
    }

    /**
     * Convenient access to the last handler return value.
     *
     * If the collection is empty, returns null. Otherwise, returns value
     * returned by last handler.
     *
     * @return mixed The last handler return value
     */
    public function last()
    {
        if (empty($this->responses)) {
            return null;
        }

        return end($this->responses);
    }

    /**
     * Check if any of the responses match the given value.
     *
     * @param  mixed $value The value to look for among responses
     * @return bool
     */
    public function contains($value)
    {
        return in_array($value, $this->responses, true);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->responses);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->responses);
    }
}