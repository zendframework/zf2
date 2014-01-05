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
use Zend\Framework\EventManager\Manager\ServicesTrait as EventManagerService;
use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\EventManager\Manager\ListenerTrait as EventManager;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager, EventManagerService, Services {
        EventManager::add insteadof Services;
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
     * @param $name
     * @return mixed
     */
    public function listener($name)
    {
        return $this->sm->get($name) ?: new $name;
    }

    /**
     *
     */
    public function run()
    {
        $event = new Event;

        $event->setTarget($this)
              ->setControllerManager($this->sm->controllerManager())
              ->setRequest($this->sm->request())
              ->setResponse($this->sm->response())
              ->setRouter($this->sm->router())
              ->setViewModel($this->sm->viewModel());

        $this->__invoke($event);
    }
}
