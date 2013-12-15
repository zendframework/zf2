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
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\Config as Config;

class ManagerFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Application
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->get(new ServiceRequest('ApplicationConfig'));

        $cm = new ControllerManager(new Config($config['controllers']));
        $cm->setServiceManager($sm);

        //if (isset($config['di']) && isset($config['di']['allowed_controllers']) && $serviceLocator->has('Di')) {
        //$controllerLoader->addAbstractFactory($serviceLocator->get('DiStrictAbstractServiceFactory'));
        //}

        return $cm;
    }
}
