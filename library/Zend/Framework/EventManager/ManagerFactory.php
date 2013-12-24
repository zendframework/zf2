<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\Manager as EventManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Manager
     */
    public function createService(ServiceManager $sm)
    {
        $listeners = $sm->getApplicationConfig()['event_manager']['listeners'];

        $em = new EventManager;

        foreach($listeners as $event => $eventListeners) {
            foreach($eventListeners as $listener) {
                if (is_string($listener)) {
                    $listener = $sm->getService($listener);
                    if ($listener instanceof ListenerInterface) {
                        $listener->setEventName($event);
                    }
                }

                $em->attach($listener);
            }
        }

        return $em;
    }
}
