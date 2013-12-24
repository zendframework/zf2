<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

abstract class AbstractPluginManager
    implements FactoryInterface
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
     * @param ServiceManager $sm
     * @return mixed|AbstractPluginManager
     */
    public function createService(ServiceManager $sm)
    {
        $this->setServiceManager($sm)
             ->setConfig(new Config($sm->getApplicationConfig()['plugins']));

        return $this;
    }

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
     * @param ServiceManagerInterface $sm
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
    public function getAlias($name)
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
        return $this->sm->getService($this->getAlias($name), $options);
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->sm->add($this->getAlias($name), $service);
        return $this;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->sm->addInvokableClass($this->getAlias($name), $class);
    }
}
