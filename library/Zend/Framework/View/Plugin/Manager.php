<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\ConfigInterface as ServiceConfig;
use Zend\Framework\Service\Factory\ServiceTrait as ServiceFactory;
use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\Service\ManagerTrait as ServiceManager;
use Zend\Framework\View\ServiceConfigTrait;

class Manager
    implements ManagerInterface, ServiceManagerInterface
{
    /**
     *
     */
    use ServiceFactory,
        ServiceManager;

    /**
     * @param array $aliases
     * @param ServiceConfig $services
     */
    public function __construct(array $aliases, ServiceConfig $services)
    {
        $this->alias    = $aliases;
        $this->services = $services;
    }
}
