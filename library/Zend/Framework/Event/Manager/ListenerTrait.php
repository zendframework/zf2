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

trait ListenerTrait
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
     * Push listener to top of queue
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function push(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = [];
            }

            if (!isset($this->listeners[$name][$priority])) {
                $this->listeners[$name][$priority][] = $listener;
                continue;
            }

            array_unshift($this->listeners[$name][$priority], $listener);
        }

        return $this;
    }

    /**
     * Add listener
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function add(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = [];
            }

            if (!isset($this->listeners[$name][$priority])) {
                $this->listeners[$name][$priority] = [];
            }

            $this->listeners[$name][$priority][] = $listener;
        }

        return $this;
    }

    /**
     * Remove listener
     *
     * @param ListenerInterface $listener
     * @return self
     */
    public function remove(ListenerInterface $listener)
    {
        $names    = $listener->names();
        $priority = $listener->priority();

        foreach($names as $name) {
            if (!isset($this->listeners[$name][$priority])) {
                continue;
            }

            $this->listeners[$name][$priority] = array_diff($this->listeners[$name][$priority], [$listener]);
        }

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
     * @param string $name
     * @param string|object $target
     * @return Generator
     */
    protected function queue($name, $target)
    {
        if (!isset($this->listeners[$name])) {
            return;
        }

        krsort($this->listeners[$name], SORT_NUMERIC);

        foreach($this->listeners[$name] as $listeners) {
            foreach($listeners as $listener) {

                //not all listeners for this priority need to be initialized
                if (is_string($listener)) {
                    $listener = $this->listener($listener);
                }

                foreach($listener->targets() as $t) {
                    if (
                        $t === ListenerInterface::WILDCARD
                        || $t === $target
                        || $target instanceof $t
                        || \is_subclass_of($target, $t)
                    ) {
                        yield $listener;
                        continue 2;
                    }
                }
            }
        }
    }

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        foreach($this->queue($event->name(), $event->target()) as $listener) {

            //var_dump($event->name().' :: '.get_class($event).' :: '.get_class($listener));

            $result = $event->__invoke($listener);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
