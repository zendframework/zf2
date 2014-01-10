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
use Zend\Framework\Service\Listener as ServiceManager;
use Zend\Framework\Event\Manager\ListenerTrait as EventListener;

trait ListenerTrait
{
    /**
     *
     */
    use EventListener;

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceManager;

        $sm->listeners($config['service_manager'])
           ->add('ServiceManager', $sm)
           ->add('ApplicationConfig', $config);

        $application = new Listener($sm);

        $sm->add('EventManager', $application);

        $application->listeners = $config['event_manager']['listeners'];

        //$mm = $sm->get('ModuleManager');
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
