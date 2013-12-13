<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\Render\Event as RenderEvent;
use Zend\Framework\Response\Event as ResponseEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class MvcListener
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = 'mvc.application';

    /**
     * @param ServiceManager $sm
     * @return mixed|MvcListener
     */
    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    /**
     * @param Event $event
     * @return mixed|void
     */
    public function __invoke(Event $event)
    {
        var_dump(__FILE__);
        $em = $event->getEventManager();

        $render = new RenderEvent;

        $render->setTarget($event->getTarget())
               ->setServiceManager($event->getServiceManager())
               ->setApplication($event->getApplication())
               ->setRequest($event->getRequest())
               ->setRouter($event->getRouter())
               ->setResponse($event->getResponse())
               ->setViewModel($event->getViewModel());

        $em->trigger($render);

        $response = new ResponseEvent;

        $response->setTarget($event->getTarget())
                 ->setServiceManager($event->getServiceManager())
                 ->setResponse($render->getResponse());

        $em->trigger($response);
    }
}
