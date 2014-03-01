<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Application\Config\ConfigInterface as Config;

class Manager
    implements ManagerInterface
{
    /**
     *
     */
    use ManagerTrait;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config   = $config;
        $this->router   = $config->router();
        $this->alias    = $this->router->plugins();
        $this->services = $config->services();
    }
}
