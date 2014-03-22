<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Generator;
use Zend\Framework\Event\Config\ConfigInterface;
use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface;

trait GeneratorTrait
{
    /**
     * @param $listener
     * @param null $options
     * @return callable|ListenerInterface
     */
    abstract protected function listener($listener, $options = null);

    /**
     * @return ConfigInterface
     */
    abstract public function listeners();

    /**
     * @param Event $event
     * @param null $options
     * @return Generator
     */
    protected function generator(Event $event, $options)
    {
        foreach($this->queue($event->event(), $options) as $listener) {
            if (!$listener instanceof ListenerInterface || $listener->target($event)) {
                yield $listener;
            }
        }
    }

    /**
     * @param string $event
     * @param null $options
     * @return Generator
     */
    protected function queue($event, $options)
    {
        foreach($this->listeners()->queue($event) as $listeners) {
            foreach($listeners as $listener) {
                yield $this->listener($listener, $options);
            }
        }
    }

    /**
     * @param Event $event
     * @param null $options
     * @param callable $callback
     * @return mixed|null
     */
    protected function generate(Event $event, $options = null, callable $callback = null)
    {
        $result = null;

        foreach($this->generator($event, $options) as $listener) {

            $result = $event->signal($listener, $options);

            if ($callback && $callback($event, $listener, $options, $result)) {
                $event->stop();
                break;
            }

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
