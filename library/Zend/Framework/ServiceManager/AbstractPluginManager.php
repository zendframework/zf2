<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\Config as Config;

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
        $service = new static();

        $service->setServiceManager($sm)
                ->setConfig(new Config($sm->getApplicationConfig()['plugins']));

        return $service;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param ServiceManagerInterface $sm
     * @return $this
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
        return $this->sm->get(new ServiceRequest($this->getAlias($name), $options));
    }

    /**
     * @param $name
     * @param $service
     * @return $this
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
