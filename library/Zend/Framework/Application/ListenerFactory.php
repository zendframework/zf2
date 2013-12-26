<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\EventManager\ListenerInterface;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class ListenerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return EventManager
     */
    public function createService(ServiceManager $sm)
    {
        $application = new Listener($sm);
        $application->setServiceManager($sm)
                    ->setEventManager($application);

        $listeners = $sm->getApplicationConfig()['event_manager']['listeners'];

        foreach($listeners as $event => $eventListeners) {
            foreach($eventListeners as $listener) {
                if (is_string($listener)) {
                    $service = $sm->getService($listener);
                    if (!$service) {
                        throw new \Exception($listener);
                    }
                    $listener = $service;
                    if ($listener instanceof ListenerInterface) {
                        $listener->setName($event);
                    }
                }

                $application->add($listener);
            }
        }

        return $application;
    }
}
