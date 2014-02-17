<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

trait EventTrait
{
    /**
     * @var bool
     */
    protected $stopped = false;

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function call(callable $listener, $options = null)
    {
        return $this->__invoke($listener, $options);
    }

    /**
     * @return string|array
     */
    public function name()
    {
        return isset($this->name) ? $this->name : static::EVENT;
    }

    /**
     * @return mixed
     */
    public function source()
    {
        return isset($this->source) ? $this->source : null;
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
     * @param $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        return $listener($this, $options);
    }
}
