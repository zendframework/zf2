<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module;

use Zend\ModuleManager\ModuleManager;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

use Zend\Framework\ServiceManager\FactoryInterface;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return mixed|ModuleManager
     */
    public function createService(ServiceManager $sm)
    {
        $modules = $sm->get(new ServiceRequest('ApplicationConfig'))['modules'];

        $em = $sm->get(new ServiceRequest('EventManager', [], false));

        $em->attach($sm->get(new ServiceRequest('ModuleManager\DefaultListeners')));
        //$em->attach($sm->get(new ServiceRequest('ServiceListener')));

        $mm = new ModuleManager($modules);

        $mm->setEventManager($em);

        $mm->setSharedEventManager($em);

        return $mm;
    }
}
