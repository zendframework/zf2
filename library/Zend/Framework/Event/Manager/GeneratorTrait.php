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

trait GeneratorTrait
{
    /**
     * @param string|Listener $listener
     * @return Listener
     */
    abstract protected function listener($listener);

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
    protected function match($event)
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
     * @param Event $event
     * @return array
     */
    protected function queue(Event $event)
    {
        return $this->listeners()->reverse($event->name());
    }

    /**
     * @param Event $event
     * @param $options
     * @return mixed
     */
    public function __invoke(Event $event, $options = null)
    {
        $result = null;

        foreach($this->match($event) as $listener) {

            $result = $event->__invoke($listener, $options);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
