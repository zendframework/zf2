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
    public $listeners = [];

    /**
     * Add listener
     *
     * @param Listener $listener
     * @param $priority
     * @return self
     */
    public function add(Listener $listener, $priority = self::PRIORITY)
    {
        $name = $listener->name();

        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        if (!isset($this->listeners[$name][$priority])) {
            $this->listeners[$name][$priority] = [];
        }

        $this->listeners[$name][$priority][] = $listener;


        return $this;
    }

    /**
     * @param $name
     * @param $priority
     * @param $listener
     * @return self
     */
    public function configure($name, $priority, $listener)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        $this->listeners[$name][$priority][] = $listener;

        return $this;
    }

    /**
     * @param $listener
     * @return mixed
     */
    public function listener($listener)
    {
        return new $listener;
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
     * Push listener to top of queue
     *
     * @param string $name
     * @param Listener $listener
     * @param int $priority
     * @return $this
     */
    public function push($name, Listener $listener, $priority = self::PRIORITY)
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
     * @return Generator
     */
    protected function listeners($event)
    {
        $target = $event->source();
        foreach($this->queue($event) as $listeners) {
            foreach($listeners as $listener) {
                //not all listeners for this priority need to be initialized
                if (is_string($listener)) {
                    $listener = $this->listener($listener);
                }

                if ($listener->matchTarget($target)) {
                    yield $listener;
                }
            }
        }
    }

    /**
     * Remove listener
     *
     * @param Listener $listener
     * @param $priority
     * @return self
     */
    public function remove(Listener $listener, $priority = self::PRIORITY)
    {
        $name = $listener->name();

        if (!isset($this->listeners[$name][$priority])) {
            return $this;
        }

        $this->listeners[$name][$priority] = array_diff($this->listeners[$name][$priority], [$listener]);

        return $this;
    }

    /**
     * @param Event $event
     * @param $options
     * @return mixed
     */
    public function trigger(Event $event, $options = null)
    {
        foreach($this->listeners($event) as $listener) {

            //var_dump(get_class($listener));

            $result = $event->trigger($listener, $options);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
