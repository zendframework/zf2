<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\Service;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManager;

interface EventInterface
    extends Event
{
    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);
}
