<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

class EventManagerFactory implements FactoryInterface
{
    /**
     * Create an EventManager instance
     *
     * Creates a new EventManager instance, seeding it with a shared instance
     * of SharedEventManager.
     *
     * @param  ServiceManager $sm
     * @return EventManager
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->get(new ServiceRequest('ApplicationConfig'));

        $em = new EventManager();

        foreach($config['events'] as $event) {
            foreach($event as $listener) {
                if (is_string($listener)) {
                    $listener = $sm->get(new ServiceRequest($listener));
                }
                $em->attach(new $listener);
            }
        }

        return $em;
    }
}
