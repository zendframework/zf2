<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap;

use Zend\Framework\Bootstrap\Event as BootstrapEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class MvcListener
    extends EventListener
    implements FactoryInterface
{
    const EVENT_NAME = 'BootstrapEvent';

    protected $name = 'mvc.application';

    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    public function __invoke(Event $event)
    {
        var_dump(__FILE__);
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $bootstrap = new BootstrapEvent;

        $bootstrap->setApplication($sm->getApplication())
                  ->setEventManager($sm->getEventManager())
                  ->setServiceManager($sm)
                  ->setRequest($sm->getRequest())
                  ->setResponse($sm->getResponse())
                  ->setRouter($sm->getRouter())
                  ->setControllerLoader($sm->getControllerLoader())
                  ->setViewModel($sm->getViewModel())
                  ->setViewConfig($sm->getViewConfig())
                  ->setViewManager($sm->getViewManager())
                  ->setViewResolver($sm->getViewResolver())
                  ->setViewPluginManager($sm->getViewPluginManager())
                  ->setView($sm->getView());

        $em->trigger($bootstrap);
    }
}
