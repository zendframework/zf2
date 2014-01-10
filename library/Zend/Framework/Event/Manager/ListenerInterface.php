<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
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
     * @param Listener $listener
     * @return self
     */
    public function push(Listener $listener);

    /**
     * Remove
     *
     * @param Listener $listener
     * @return self
     */
    public function remove(Listener $listener);
}
