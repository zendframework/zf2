<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\ServiceManager\ConfigInterface as Config;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\ManagerInterface as ViewManager;

class Manager
    implements ViewManager
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
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @return Config
     */
    public function getViewConfig()
    {
        return $this->config;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->config->get($name);
    }

    /**
     * @return array
     */
    public function getViewHelpers()
    {
        return $this->config->getViewHelpers();
    }

    /**
     * @return array
     */
    public function getMvcStrategies()
    {
        return $this->config->getMvcStrategies();
    }

    /**
     * @return array
     */
    public function getStrategies()
    {
        return $this->config->getStrategies();
    }

    /**
     * @return string
     */
    public function getLayoutTemplate()
    {
        return $this->config->getLayoutTemplate();
    }

    /**
     * @return mixed
     */
    public function getViewResolver()
    {
        return $this->sm->getViewResolver();
    }

    /**
     * @param $resolver
     * @return $this
     */
    public function setViewResolver($resolver)
    {
        $this->sm->setViewResolver($resolver);
        return $this;
    }
}
