<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventInterface as Event;

use Zend\Framework\EventManager\ManagerInterface as EventManagerInterface;

class Manager
    extends PriorityQueueListener
    implements EventManagerInterface
{
    /**
     * @param EventInterface $event
     * @return void
     */
    public function trigger(Event $event)
    {
        $this->__invoke($event);
    }
}
