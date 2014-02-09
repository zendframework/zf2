<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Generator;
use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Event\ListenerTrait as EventListener;

trait ManagerTrait
{
    /**
     *
     */
    use EventListener;

    /**
     * Listeners
     *
     * @var array Listener
     */
    protected $listeners = [];

    /**
     * @param string $name
     * @param string|Listener $listener
     * @param $priority
     * @return self
     */
    public function add($name, $listener, $priority = ManagerInterface::PRIORITY)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        $this->listeners[$name][$priority][] = $listener;

        return $this;
    }

    /**
     * @param array $listeners
     * @return self
     */
    public function config(array $listeners)
    {
        $this->listeners = $listeners;
        return $this;
    }

    /**
     * @param string|Event $event
     * @return mixed
     */
    abstract public function event($event);

    /**
     * @param string|Listener $listener
     * @return mixed
     */
    abstract public function listener($listener);

    /**
     * @param array $listeners
     * @return Generator
     */
    protected function generator(array $listeners)
    {
        foreach($listeners as $listener) {
            yield $this->listener($listener);
        }
    }

    /**
     * @param Event $event
     * @return Generator
     */
    protected function listeners($event)
    {
        foreach($this->queue($event) as $listeners) {
            foreach($this->generator($listeners) as $listener) {
                if ($listener->target($event)) {
                    yield $listener;
                }
            }
        }
    }

    /**
     * Push listener to top of queue
     *
     * @param string $name
     * @param string|Listener $listener
     * @param int $priority
     * @return self
     */
    public function push($name, $listener, $priority = ManagerInterface::PRIORITY)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        if (!isset($this->listeners[$name][$priority])) {
            $this->listeners[$name][$priority][] = $listener;
            return $this;
        }

        array_unshift($this->listeners[$name][$priority], $listener);

        return $this;
    }

    /**
     * @param Event $event
     * @return array
     */
    protected function queue(Event $event)
    {
        $name = $event->name();

        if (!isset($this->listeners[$name])) {
            return [];
        }

        krsort($this->listeners[$name], SORT_NUMERIC);

        return $this->listeners[$name];
    }

    /**
     * @param string|Listener $listener
     * @return self
     */
    public function remove($listener)
    {
        foreach($this->listeners as $name => $listeners) {
            foreach(array_keys($listeners) as $priority) {
                $this->listeners[$name][$priority] = array_diff($this->listeners[$name][$priority], [$listener]);
            }
        }

        return $this;
    }

    /**
     * @param string|Event $event
     * @param null $options
     * @return mixed
     */
    public function trigger($event, $options = null)
    {
        return $this->__invoke($this->event($event), $options);
    }

    /**
     * @param Event $event
     * @param $options
     * @return mixed
     */
    public function __invoke(Event $event, $options = null)
    {
        $result = null;

        foreach($this->listeners($event) as $listener) {

            //var_dump(get_class($listener));

            $result = $event->__invoke($listener, $options);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
