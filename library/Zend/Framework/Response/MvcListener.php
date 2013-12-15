<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\Response\Event as ResponseEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

class MvcListener
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_NAME;

    /**
     * @param ServiceManager $sm
     * @return mixed|MvcListener
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * @param Event $event
     * @return mixed|void
     */
    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();

        $response = new ResponseEvent;

        $response->setTarget($event->getTarget())
                 ->setServiceManager($event->getServiceManager());

        //delayed due circular dependency in the application initialization
        $em->attach($event->getServiceManager()->get(new ServiceRequest('DefaultRenderingStrategy')));

        $em->trigger($response);
    }
}
