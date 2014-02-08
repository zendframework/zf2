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
use Zend\Framework\Event\ListenerInterface;

interface ManagerInterface
    extends ListenerInterface
{
    /**
     * Default priority
     *
     */
    const PRIORITY = 0;

    /**
     * @param string $name
     * @param string|ListenerInterface $listener
     * @param $priority
     * @return self
     */
    public function add($name, $listener, $priority = self::PRIORITY);

    /**
     * @param $listeners
     * @return self
     */
    public function config(array $listeners);

    /**
     * @param ListenerInterface $listener
     * @return self
     */
    public function remove(ListenerInterface $listener);

    /**
     * Push listener to top of queue
     *
     * @param string $name
     * @param string|ListenerInterface $listener
     * @param int $priority
     * @return self
     */
    public function push($name, $listener, $priority = self::PRIORITY);

    /**
     * @param string|EventInterface $event
     * @param $options
     * @return mixed
     */
    public function trigger($event, $options = null);
}
