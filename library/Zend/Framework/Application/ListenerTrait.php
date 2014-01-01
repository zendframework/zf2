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
use Zend\Framework\EventManager\PriorityQueue\ListenerTrait as PriorityQueue;
use Zend\Framework\EventManager\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\ListenerConfig  as Config;
use Zend\Framework\Service\ServicesTrait as Services;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager, PriorityQueue, Services {
        PriorityQueue::add insteadof Services;
        Services::add as addService;
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
     * @param $name
     * @return mixed
     */
    public function listener($name)
    {
        return $this->sm->get($name);

    }

    /**
     *
     */
    public function run()
    {
        $event = new Event;

        $event->setTarget($this)
              ->setServiceManager($this->sm)
              ->setEventManager($this);

        $this->__invoke($event);
    }
}
