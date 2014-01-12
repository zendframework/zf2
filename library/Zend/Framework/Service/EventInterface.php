<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Framework\Event\ListenerInterface;

interface EventInterface
    extends Event
{
    /**
     * @return string
     */
    public function alias();

    /**
     * @return array
     */
    public function options();
    /**
     * @return string
     */
    public function service();

    /**
     * @return bool
     */
    public function shared();

    /**
     * Trigger
     *
     * @param ListenerInterface $listener
     * @return bool|callable
     */
    public function __invoke(ListenerInterface $listener);
}
