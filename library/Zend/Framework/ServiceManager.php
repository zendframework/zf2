<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ServiceManager\ServiceRequest;

use Zend\Framework\ApplicationInterface as Application;
use Zend\Framework\ManagerInterface as ViewManager;
use Zend\Framework\EventManager\EventManagerInterface as EventManager;

class ServiceManager extends ServiceManager\ServiceManager
{
    /**
     * @param $name
     * @return bool|object
     */
    public function getService($name)
    {
        return $this->get(new ServiceRequest($name));
    }

    /**
     * @return array
     */
    public function getApplicationConfig()
    {
        return $this->getService('ApplicationConfig');
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->getService('Application');
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->getService('EventManager');
    }

    /**
     * @return ViewManager
     */
    public function getViewManager()
    {
        return $this->getService('ViewManager');
    }

    public function getViewResolver()
    {
        return $this->getService('ViewResolver');
    }

    public function setViewResolver($resolver)
    {
        return $this->add('ViewResolver', $resolver);
    }
}
