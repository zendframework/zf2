<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ServiceManager;
use Zend\Framework\ServiceManager\Config as ServiceManagerConfig;
use Zend\Framework\View\Model\ViewModel;
use Zend\ModuleManager;

class Application
    implements ApplicationInterface
{
    /**
     *
     */
    const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    const ERROR_EXCEPTION                  = 'error-exception';
    const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->sm;
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->sm->getEventManager();
    }

    /**
     * @param array $config
     * @return Application
     */
    public static function init(array $config = [])
    {
        //$config = new ApplicationConfig($config);

        $sm = new ServiceManager(new ServiceManagerConfig($config['service_manager']));

        $sm->setApplicationConfig($config)
           ->setViewModel(new ViewModel);

        //$mm = $sm->get(new ServiceRequest('ModuleManager'));
        //$mm->loadModules();

        $application = new self($sm);

        $sm->setApplication($application);

        return $application;
    }

    /**
     * @return $this
     */
    public function run()
    {
        $sm = $this->getServiceManager();
        $em = $this->getEventManager();

        $event = new MvcEvent;

        $event->setTarget($this)
              ->setServiceManager($sm)
              ->setEventManager($em);

        $em->trigger($event);

        return $this;
    }
}
