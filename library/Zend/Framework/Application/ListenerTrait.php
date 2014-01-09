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
use Zend\Framework\Event\Manager\ListenerTrait as EventListener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\Service\ServiceTrait as Service;

trait ListenerTrait
{
    /**
     *
     */
    use EventListener,
        EventManager,
        Service {
            EventListener::add insteadof Service;
        }

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceManager;

        $sm->listeners($config['service_manager'])
           ->setApplicationConfig($config);

        $application = new Listener($sm);

        $sm->setEventManager($application);

        $application->listeners = $config['event_manager']['listeners'];

        //$mm = $sm->service('ModuleManager');
        //$mm->loadModules();

        return $application;
    }

    /**
     * Pull listener from service manager
     *
     * @param $listener
     * @return mixed
     */
    public function listener($listener)
    {
        return $this->sm->get($listener);
    }

    /**
     *
     */
    public function run()
    {
        $event = new Event;

        $event->setServiceManager($this->sm);

        $this->__invoke($event);
    }
}
