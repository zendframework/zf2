<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\Manager\ConfigInterface as EventConfig;
use Zend\Framework\Service\ConfigInterface as ServiceConfig;
use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\ManagerTrait as ServiceManager;

class Manager
    implements ManagerInterface, ServiceManagerInterface
{
    /**
     *
     */
    use ManagerTrait,
        ServiceManager;

    /**
     * @param ServiceConfig $services
     * @param EventConfig $listeners
     */
    public function __construct(ServiceConfig $services, EventConfig $listeners)
    {
        $this->services  = $services;
        $this->listeners = $listeners;
    }
}
