<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Exception;
use Zend\Framework\View\Error\Event as RenderErrorEvent;
use Zend\Framework\View\Event as RenderEvent;
use Zend\Framework\EventManager\ListenerTrait;
use Zend\Framework\Mvc\EventInterface;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_SERVICE, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $sm = $event->getServiceManager();
        $em = $event->getEventManager();

        $render = new RenderEvent;

        $render->setTarget($event->target())
               ->setServiceManager($sm)
               ->setView($sm->getView());

        //parent view model
        $render->setViewModel($sm->getViewModel());

        try {

            $em->__invoke($render);

        } catch(Exception $exception) {

            $error = new RenderErrorEvent;

            $error->setTarget($event->target())
                  ->setException($exception);

            $em->__invoke($error);
        }
    }
}
