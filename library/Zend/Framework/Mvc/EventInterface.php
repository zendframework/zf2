<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

interface EventInterface
    extends Event
{
    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     * @return self
     */
    public function setResult($result);

    /**
     * @return EventManager
     */
    public function getEventManager();

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);
}
