<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Event\Manager\Config as EventConfig;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\Config as ServiceConfig;

class Factory
{
    /**
     *
     */
    use Config,
        EventManager;

    /**
     * @param array $config
     * @return EventManagerInterface
     */
    public static function factory(array $config)
    {
        $services  = new ServiceConfig($config['service_manager']);
        $listeners = new EventConfig($config['event_manager']['listeners']);

        $application = new Manager($services, $listeners);

        $application->add('AppConfig', $config)
                    ->add('EventManager', $application);

        return $application;
    }
}
