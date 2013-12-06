<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\ServiceListenerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceRequest;

class ModuleManagerFactory implements ServiceListenerInterface
{
    /**
     * Creates and returns the module manager
     *
     * Instantiates the default module listeners, providing them configuration
     * from the "module_listener_options" key of the ApplicationConfig
     * service. Also sets the default config glob path.
     *
     * Module manager is instantiated and provided with an EventManager, to which
     * the default listener aggregate is attached. The ModuleEvent is also created
     * and attached to the module manager.
     *
     * @param  ServiceManager $sm
     * @return ModuleManager
     */
    public function __invoke(ServiceManager $sm)
    {
        $modules = $sm->get(new ServiceRequest('ApplicationConfig'))['modules'];

        $em = $sm->get(new ServiceRequest('EventManager', [], false));

        $em->attach($sm->get(new ServiceRequest('ModuleManager\DefaultListeners')));
        $em->attach($sm->get(new ServiceRequest('ServiceListener')));

        $mm = new ModuleManager($modules);

        $mm->setEventManager($em);

        $mm->setSharedEventManager($sm->get(new ServiceRequest('SharedEventManager')));

        return $mm;
    }
}
