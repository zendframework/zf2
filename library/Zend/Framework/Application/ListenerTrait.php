<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\EventManager\Manager\ListenerTrait as ManagerService;
use Zend\Framework\Mvc\Event as MvcEvent;
use Zend\Framework\ServiceManager\Config  as ServiceManagerConfig;
use Zend\Framework\ServiceManager;
use Zend\Framework\ServiceManager\ServiceManagerInterface;

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
     * @return Application
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceManager(new ServiceManagerConfig($config['service_manager']));

        $sm->setApplicationConfig($config);

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
