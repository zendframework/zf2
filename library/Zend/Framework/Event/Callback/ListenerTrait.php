<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Callback;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerTrait as Listener;

trait ListenerTrait
{
    /**
     *
     */
    use Listener {
        Listener::__construct as listener;
    }

    /**
     * Callback
     *
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param $callback
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($callback, $event = null, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
        $this->setCallback($callback);
    }

    /**
     * Callback
     *
     * @return callable
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * Callback set
     *
     * @param $callback
     * @return self
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        return call_user_func($this->callback, $event);
    }
}
