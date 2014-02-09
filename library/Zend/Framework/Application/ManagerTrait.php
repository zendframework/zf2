<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;

trait ManagerTrait
{
    /**
     *
     */
    use EventManager;

    /**
     * Retrieve event from service manager
     *
     * @param array|EventInterface|string $event
     * @return EventInterface
     */
    public function event($event)
    {
        return $event instanceof EventInterface ? $event : $this->get($event);
    }

    /**
     * Retrieve listener from service manager
     *
     * @param array|ListenerInterface|string $listener
     * @return ListenerInterface
     */
    public function listener($listener)
    {
        return $listener instanceof ListenerInterface ? $listener : $this->get($listener);
    }

    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function run($event = self::EVENT_APPLICATION, $options = null)
    {
        return $this->trigger($event, $options);
    }

    /**
     * @param string|array|EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function trigger($event, $options = null)
    {
        return $this->__invoke($this->event($event), $options);
    }
}
