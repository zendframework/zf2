<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Mvc\Controller\ControllerManager as ControllerManager;
use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;

trait ServicesTrait
{
    /**
     * @return bool|ControllerPluginManager
     */
    public function controllerPluginManager()
    {
        return $this->service('Controller\Plugin\Manager');
    }

    /**
     * @return bool|ControllerManager
     */
    public function controllerManager()
    {
        return $this->service('Controller\Manager');
    }

    /**
     * @param ControllerManager $cm
     * @return self
     */
    public function setControllerManager(ControllerManager $cm)
    {
        return $this->add('Controller\Manager', $cm);
    }
}
