<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ApplicationInterface;
use Zend\Framework\ServiceManager;
use Zend\Framework\ServiceManager\Config as ServiceManagerConfig;
use Zend\ModuleManager;
use Zend\Framework\View\Model\ViewModel;

class Application implements
    ApplicationInterface
{

    const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    const ERROR_EXCEPTION                  = 'error-exception';
    const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';

    protected $sm;

    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    public function getEventManager()
    {
        return $this->sm->getEventManager();
    }

    /**
     * @param array $config
     * @return mixed
     */
    public static function init($config = array())
    {
        //$config = new ApplicationConfig($config);

        $sm = new ServiceManager(new ServiceManagerConfig($config['service_manager']));

        $sm->add('ApplicationConfig', $config);

        $sm->add('ViewModel', new ViewModel);

        //$mm = $sm->get(new ServiceRequest('ModuleManager'));
        //$mm->loadModules();

        $application = new self($sm);

        $sm->add('Application', $application);

        return $application;
    }

    /**
     * @return $this|Response
     */
    public function run()
    {
        $sm = $this->getServiceManager();
        $em = $this->getEventManager();

        $event = new MvcEvent;

        $event->setTarget($this)
              ->setApplication($this)
              ->setServiceManager($sm)
              ->setEventManager($em)
              ->setRequest($sm->getRequest())
              ->setResponse($sm->getResponse())
              ->setRouter($sm->getRouter())
              ->setControllerLoader($sm->getControllerLoader())
              ->setViewModel($sm->getViewModel());

        $event->setCallback(function($event, $listener, $response) {

        });

        $em->trigger($event);

        return $this;
    }
}
