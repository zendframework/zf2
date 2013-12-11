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

class Manager implements ViewManager
{
    protected $config;
    protected $resolver;
    protected $sm;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    public function getViewConfig()
    {
        return $this->config;
    }

    public function getConfig($name)
    {
        return $this->config->get($name);
    }

    public function getViewHelpers()
    {
        return (array) $this->getConfig('view_helpers');
    }

    public function getMvcStrategies()
    {
        return (array) $this->getConfig('mvc_strategies');
    }

    public function getStrategies()
    {
        return (array) $this->getConfig('strategies');
    }

    public function getLayoutTemplate()
    {
        return $this->getConfig('layout_template');
    }

    public function getViewResolver()
    {
        return $this->sm->getViewResolver();
    }

    public function setViewResolver($resolver)
    {
        $this->sm->setViewResolver($resolver);
        return $this;
    }
}
