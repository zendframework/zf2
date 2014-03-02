<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Application\Config\ConfigInterface;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\ManagerTrait as ServiceManager;

class Manager
    implements ManagerInterface
{
    /**
     *
     */
    use Factory,
        ServiceManager;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config   = $config;
        $this->alias    = $config->view()->aliases();
        $this->services = $config->services();
    }
}
