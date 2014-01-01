<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\Manager;

use Zend\Framework\EventManager\PriorityQueue\EventListenerInterface as PriorityQueueInterface;
use Zend\Framework\EventManager\PriorityQueue\ListenerInterface as PriorityListenerInterface;

class Listener
    implements ListenerInterface,
               EventListenerInterface,
               PriorityListenerInterface,
               PriorityQueueInterface
{
    /**
     *
     */
    use ListenerTrait;
}
