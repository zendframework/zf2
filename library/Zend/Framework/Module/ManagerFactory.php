<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module;

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\ModuleManager\ModuleManager;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return mixed|ModuleManager
     */
    public function createService(ServiceManager $sm)
    {
        $modules = $sm->getApplicationConfig()['modules'];

        $em = $sm->getEventManager();

        $em->attach($sm->getService('ModuleManager\DefaultListeners'));

        $mm = new ModuleManager($modules);

        $mm->setEventManager($em);

        //$mm->setSharedEventManager($em);

        return $mm;
    }
}
