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
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

use Zend\Framework\ServiceManager\FactoryInterface;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return EventManager
     */
    public function createService(ServiceManager $sm)
    {
        $listeners = $sm->getApplicationConfig()['event_manager']['listeners'];

        $em = new EventManager();

        foreach($listeners as $event => $eventListeners) {

            foreach($eventListeners as $listener) {

                if (is_string($listener)) {
                    $listener = $sm->get(new ServiceRequest($listener));
                }

                $em->attach($listener);
            }

        }

        return $em;
    }
}
