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
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_RESPONSE;

    /**
     * @param ServiceManager $sm
     * @return mixed|Listener
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
        $sm = $event->getServiceManager();
        $em = $event->getEventManager();

        $render = new RenderEvent;

        $render->setTarget($event->getTarget())
               ->setServiceManager($sm);

        //set root view model
        //$render->setViewModel($sm->getViewModel());

        try {

            $em->trigger($render);

        } catch(Exception $exception) {

            $error = new RenderErrorEvent;

            $error->setTarget($event->getTarget())
                  ->setException($exception->getPrevious());

            $em->trigger($error);
        }
    }
}
