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
use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\ListenerTrait as Listener;
use Zend\Framework\Event\ResultInterface as Result;

trait ManagerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * Listeners
     *
     * @var array ListenerInterface
     */
    public $listeners = [];

    /**
     * Add listener
     *
     * @param ListenerInterface $listener
     * @param $priority
     * @return self
     */
    public function add(ListenerInterface $listener, $priority = self::PRIORITY)
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
     * @param $name
     * @return array
     */
    protected function listeners($name)
    {
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
     * @param ListenerInterface $listener
     * @param int $priority
     * @return $this
     */
    public function push($name, ListenerInterface $listener, $priority = self::PRIORITY)
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
     * @param string $name
     * @param string $target
     * @return Generator
     */
    protected function queue($name, $target)
    {
        foreach($this->listeners($name) as $listeners) {
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
     * @param ListenerInterface $listener
     * @param $priority
     * @return self
     */
    public function remove(ListenerInterface $listener, $priority = self::PRIORITY)
    {
        $name = $listener->name();

        if (!isset($this->listeners[$name][$priority])) {
            return $this;
        }

        $this->listeners[$name][$priority] = array_diff($this->listeners[$name][$priority], [$listener]);

        return $this;
    }

    /**
     * @param EventInterface $event
     * @param mixed $result
     * @return mixed
     */
    public function trigger(EventInterface $event, $result = null)
    {
        $name   = $event->name();
        $source = $event->source();

        foreach($this->queue($name, $source) as $listener) {

            $result = $listener->trigger($event, $result);

            if ($result == ListenerInterface::STOPPED) {
                break;
            }

            if ($result instanceof Result) {
                $result = $result->result();
                break;
            }
        }

        return $result;
    }
}
