<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\EventManager\Manager\ListenerInterface as EventManagerInterface;
use Zend\Framework\EventManager\PriorityQueue\EventListenerInterface as PriorityQueueInterface;
use Zend\Framework\EventManager\PriorityQueue\ListenerInterface as PriorityListenerInterface;
use Zend\Framework\EventManager\PriorityQueue\Shared\ListenerInterface as SharedListenerInterface;

class Listener
    implements ListenerInterface,
               EventManagerInterface,
               PriorityListenerInterface,
               PriorityQueueInterface,
               SharedListenerInterface
{
    /**
     *
     */
    use ListenerTrait;
}
