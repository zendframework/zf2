<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\Manager\Config as ListenersConfig;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\Config as ServiceConfig;

class Factory
{
    /**
     *
     */
    use EventManager;

    /**
     * @param array $config
     * @return EventManagerInterface
     */
    public static function factory(array $config)
    {
        $services  = new ServiceConfig($config['service_manager']);
        $listeners = new ListenersConfig($config['event_manager']);

        $application = new Manager($services, $listeners);

        $application->add('Config', $config)
                    ->add('EventManager', $application);

        return $application;
    }
}
