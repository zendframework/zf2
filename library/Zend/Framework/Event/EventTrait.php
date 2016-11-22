<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

trait EventTrait
{
    /**
     * @var mixed
     */
    protected $source;

    /**
     * @var bool
     */
    protected $stopped = false;

    /**
     * Event name
     *
     * @return string
     */
    public function event()
    {
        return isset($this->event) ? $this->event : static::EVENT;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function signal(callable $listener, $options = null)
    {
        return $this->__invoke($listener, $options);
    }

    /**
     * @return mixed
     */
    public function source()
    {
        return $this->source;
    }

    /**
     * @return self
     */
    public function stop()
    {
        $this->stopped = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function stopped()
    {
        return $this->stopped;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        return $listener($this, $options);
    }
}
