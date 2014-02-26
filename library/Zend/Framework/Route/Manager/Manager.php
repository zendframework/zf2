<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Route\ConfigInterface as RouterConfig;
use Zend\Framework\Service\ConfigInterface as ServiceConfig;
use Zend\Framework\Service\Factory\ServiceTrait as ServiceFactory;
use Zend\Framework\Service\ManagerTrait as ServiceManager;

class Manager
    implements ManagerInterface, ServiceManagerInterface
{
    /**
     *
     */
    use ManagerTrait,
        ServiceFactory,
        ServiceManager;

    /**
     * @param RouterConfig $config
     * @param ServiceConfig $services
     */
    public function __construct(RouterConfig $config, ServiceConfig $services)
    {
        $this->config   = $config->configuration();
        $this->alias    = $config->plugins();
        $this->services = $services;
    }
}
