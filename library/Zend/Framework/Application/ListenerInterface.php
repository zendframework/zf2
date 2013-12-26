<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\ServiceManager\ServiceLocatorInterface as ServiceManager;
use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;

interface ListenerInterface
{
    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event);

    /**
     * @return EventManager
     */
    public function getEventManager();

    /**
     * @return ServiceManager
     */
    public function getServiceManager();
}
