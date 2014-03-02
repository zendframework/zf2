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
use Zend\Framework\Event\ListenerInterface;

trait GeneratorTrait
{
    /**
     * @param $listener
     * @return callable|ListenerInterface
     */
    abstract protected function listener($listener);

    /**
     * @return ConfigInterface
     */
    abstract public function listeners();

    /**
     * @param Event $event
     * @return Generator
     */
    protected function generator(Event $event)
    {
        foreach($this->queue($event->event()) as $listener) {
            if (!$listener instanceof ListenerInterface || $listener->target($event)) {
                yield $listener;
            }
        }
    }

    /**
     * @param string $event
     * @return Generator
     */
    protected function queue($event)
    {
        foreach($this->listeners()->get($event) as $listeners) {
            foreach($listeners as $listener) {
                yield $this->listener($listener);
            }
        }
    }

    /**
     * @param Event $event
     * @param null $options
     * @return mixed|null
     */
    public function __invoke(Event $event, $options = null)
    {
        $result = null;

        foreach($this->generator($event) as $listener) {

            $result = $event->signal($listener, $options);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
