<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\Listener\DefaultListeners;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

use Zend\Framework\ServiceManager\FactoryInterface;

class DefaultListenersFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return DefaultListeners
     */
    public function createService(ServiceManager $sm)
    {
        return new DefaultListeners(new ListenerOptions($sm->get(new ServiceRequest('ApplicationConfig'))['module_listener_options']));
    }
}
