<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Exception;
use Zend\Framework\EventManager\ListenerInterface;
use Zend\Framework\EventManager\Manager\ListenerTrait as ManagerService;
use Zend\Framework\Mvc\Event as MvcEvent;
use Zend\Framework\Service\ListenerConfig  as ServiceConfig;
use Zend\Framework\Mvc\Service\Listener as ServiceManager;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManagerInterface;
use \Zend\Framework\Service\Listener as ServiceListener;

trait ListenerTrait
{
    /**
     *
     */
    use ManagerService, ServiceTrait;

    /**
     * @param ServiceManagerInterface $sm
     */
    public function __construct(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceManager(new ServiceConfig($config['service_manager']));

        $sm->setApplicationConfig($config);

        $application = new Listener($sm);

        $sm->setEventManager($application);

        $application->setEventManager($application);

        $application->add(new ServiceListener);

        $listeners = $config['event_manager']['listeners'];

        foreach($listeners as $event => $eventListeners) {
            foreach($eventListeners as $listener) {
                if (is_string($listener)) {
                    $service = $sm->getService($listener);
                    if (!$service) {
                        throw new Exception($listener);
                    }
                    $listener = $service;
                    if ($listener instanceof ListenerInterface) {
                        $listener->setName($event);
                    }
                }

                $application->add($listener);
            }
        }

        return $application;

        //$mm = $sm->getService('ModuleManager');
        //$mm->loadModules();

        return $sm->getApplication();
    }

    /**
     *
     */
    public function run()
    {
        $sm = $this->getServiceManager();

        $event = new MvcEvent;

        $event->setTarget($this)
              ->setServiceManager($sm)
              ->setEventManager($this);

        $this->__invoke($event);
    }
}
