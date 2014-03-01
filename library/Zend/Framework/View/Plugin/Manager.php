<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Application\Config\ConfigInterface as Config;
use Zend\Framework\Service\Factory\ServiceTrait as ServiceFactory;
use Zend\Framework\Service\ManagerTrait as ServiceManager;
use Zend\Framework\View\ServiceConfigTrait;

class Manager
    implements ManagerInterface
{
    /**
     *
     */
    use ServiceFactory,
        ServiceManager;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config   = $config;
        $this->alias    = $config->view()->aliases();
        $this->services = $config->services();
    }
}
