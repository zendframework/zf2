<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Render\Mvc;

use Exception;
use Zend\Framework\Render\Error\Event as RenderErrorEvent;
use Zend\Framework\Render\Event as RenderEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_MVC_APPLICATION, $target = null, $priority = null)
    {
        $this->eventName = $event;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $sm = $event->getServiceManager();
        $em = $event->getEventManager();

        $render = new RenderEvent;

        $render->setEventTarget($event->getEventTarget())
               ->setServiceManager($sm)
               ->setView($sm->getView());

        //parent view model
        $render->setViewModel($sm->getViewModel());

        try {

            $em->trigger($render);

        } catch(Exception $exception) {

            $error = new RenderErrorEvent;

            $error->setEventTarget($event->getEventTarget())
                  ->setException($exception->getPrevious());

            $em->trigger($error);
        }
    }
}
