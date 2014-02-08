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
     * Default priority
     *
     */
    const PRIORITY = 0;

    /**
     * @param string $name
     * @param Listener $listener
     * @param $priority
     * @return self
     */
    public function add($name, Listener $listener, $priority = self::PRIORITY);

    /**
     * @param $listeners
     * @return self
     */
    public function config(array $listeners);

    /**
     * @param Listener $listener
     * @return self
     */
    public function remove(Listener $listener);

    /**
     * @param EventInterface $event
     * @param $options
     * @return mixed
     */
    public function trigger(EventInterface $event, $options = null);
}
