<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Framework\ServiceManager\ServiceManager;

class ControllerManager
{

    /**
     * @var ServiceManager
     */
    protected $sm;

    public function setServiceLocator(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @param $service
     * @return object
     */
    public function get($service)
    {
        return $this->sm->get($service);
    }

    public function getConfig($name)
    {
        return $this->sm->getConfig($name);
    }
}
