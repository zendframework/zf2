<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\View\Config as ViewConfig;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Manager as ViewManager;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Manager
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->get(new ServiceRequest('ApplicationConfig'))['view_manager'];

        $vm = new ViewManager(new ViewConfig($config));
        $vm->setServiceManager($sm);

        return $vm;
    }
}
