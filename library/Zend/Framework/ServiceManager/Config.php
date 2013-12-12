<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\ServiceManagerInterface;
use Zend\Framework\ServiceManager\ConfigInterface;

class Config implements ConfigInterface
{

    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get($name, $default = null)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed $config
     * @return $this
     */
    public function add($name, $config)
    {
        $this->config[$name] = $config;
        return $this;
    }

    /**
     * Configure service manager
     *
     * @param ServiceManager $serviceManager
     * @return void
     */
    public function __invoke(ServiceManagerInterface $serviceManager)
    {
        $serviceManager->setConfig($this);
    }
}
