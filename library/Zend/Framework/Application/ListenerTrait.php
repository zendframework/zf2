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
use Zend\Framework\Application\Service\Listener as ServiceManager;
use Zend\Framework\EventManager\ListenerInterface;
use Zend\Framework\EventManager\PriorityQueue\ListenerTrait as EventManager;
use Zend\Framework\Mvc\Event as MvcEvent;
use Zend\Framework\Service\ListenerConfig  as Config;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager;

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceManager;

        $sm->setConfig(new Config($config['service_manager']))
           ->setApplicationConfig($config);

        $application = new Listener($sm);

        $sm->setEventManager($application);

        //$application->setEventManager($application);

        //Service Listener
        //$application->add($sm);

        $listeners = $config['event_manager']['listeners'];

        foreach($listeners as $event => $eventListeners) {
            foreach($eventListeners as $listener) {
                if (is_string($listener)) {
                    $service = $sm->get($listener);
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

        //$mm = $sm->service('ModuleManager');
        //$mm->loadModules();

        return $application;
    }

    /**
     *
     */
    public function run()
    {
        $event = new MvcEvent;

        $event->setTarget($this)
              ->setServiceManager($this->sm)
              ->setEventManager($this);

        $this->__invoke($event);
    }
}
