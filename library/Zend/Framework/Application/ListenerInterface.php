<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Framework\EventManager\EventInterface;

interface ListenerInterface
{
    /**
     * @return EventManager
     */
    public function eventManager();

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @return ServiceManager
     */
    public function serviceManager();

    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event);
}
