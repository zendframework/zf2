<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Service\ListenerConfigInterface as Config;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Framework\View\ManagerInterface as ViewManager;
use Zend\View\Resolver\ResolverInterface as ViewResolver;

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
     * @return self
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @return Config
     */
    public function viewConfig()
    {
        return $this->config;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->config->get($name);
    }

    /**
     * @return array
     */
    public function viewHelpers()
    {
        return $this->config->viewHelpers();
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return $this->config->layoutTemplate();
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->config->displayExceptions();
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->config->displayNotFoundReason();
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return $this->config->exceptionTemplate();
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->config->notFoundTemplate();
    }

    /**
     * @return ViewResolver
     */
    public function viewResolver()
    {
        return $this->sm->viewResolver();
    }

    /**
     * @param ViewResolver $resolver
     * @return self
     */
    public function setViewResolver(ViewResolver $resolver)
    {
        $this->sm->setViewResolver($resolver);
        return $this;
    }
}
