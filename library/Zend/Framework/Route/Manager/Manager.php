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
use Zend\Framework\Route\Config\ConfigInterface as RouterConfig;
use Zend\Framework\Service\AliasTrait as Alias;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;

class Manager
    implements ManagerInterface
{
    /**
     *
     */
    use Alias,
        Factory,
        ServiceManager;

    /**
     * @var RouterConfig
     */
    protected $router;

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

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function route($name, $options = null)
    {
        return $this->create($this->alias($name), $options);
    }
}
