<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\ListenerConfig as Config;
use Zend\Framework\Service\ListenerInterface as ServiceManager;

/**
 * Plugin manager implementation for view helpers
 *
 * Enforces that helpers retrieved are instances of
 * Helper\HelperInterface. Additionally, it registers a number of default
 * helpers.
 */
class Manager
    implements ManagerInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param Config $config
     * @return self
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @param $name
     * @return string
     */
    public function alias($name)
    {
        return $this->config->get(strtolower($name));
    }

    /**
     * @param $name
     * @param $options
     * @return mixed
     */
    public function get($name, $options)
    {
        return $this->sm->service($this->alias($name), $options);
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->sm->add($this->alias($name), $service);
        return $this;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->sm->addInvokableClass($this->alias($name), $class);
    }
}
