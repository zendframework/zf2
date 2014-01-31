<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Route;

use Zend\Framework\Application\EventInterface as ApplicationEvent;
use Zend\Framework\Event\EventInterface as Event;

interface EventInterface
    extends Event
{
    /**
     * Trigger
     *
     * @param ApplicationEvent $event
     * @param $options
     * @return mixed
     */
    public function trigger(ApplicationEvent $event, $options = null);
}
