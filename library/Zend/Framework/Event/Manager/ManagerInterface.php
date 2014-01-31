<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface as Listener;

interface ManagerInterface
    extends Listener
{
    /**
     * Priority default
     *
     */
    const PRIORITY = 0;

    /**
     * Add
     *
     * @param Listener $listener
     * @return self
     */
    public function add(Listener $listener);

    /**
     * @param $name
     * @param $priority
     * @param $listener
     * @return self
     */
    public function configure($name, $priority, $listener);

    /**
     * @param $listener
     * @return mixed
     */
    public function listener($listener);

    /**
     * Push listener to top of queue
     *
     * @param string $name
     * @param Listener $listener
     * @param int $priority
     * @return $this
     */
    public function push($name, Listener $listener, $priority = self::PRIORITY);

    /**
     * Remove
     *
     * @param Listener $listener
     * @return self
     */
    public function remove(Listener $listener);

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @param $options
     * @return mixed
     */
    public function trigger(EventInterface $event, $options = null);
}
