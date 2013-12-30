<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\Controller\Manager as ControllerManager;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Framework\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Service\ListenerConfig as Config;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return ControllerManager
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->applicationConfig();

        $cm = new ControllerManager(new Config($config['controllers']));
        $cm->setServiceManager($sm);

        return $cm;
    }
}
