<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\Framework\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Framework\View\Config as ViewConfig;

class ListenerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->applicationConfig()['view_manager'];

        $vm = new Listener(new ViewConfig($config));
        $vm->setServiceManager($sm);

        return $vm;
    }
}
