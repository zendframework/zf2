<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class ControllerManager
{
    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceLocator(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($name);
    }
}
