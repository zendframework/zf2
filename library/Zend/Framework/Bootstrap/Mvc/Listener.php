<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap\Mvc;

use Zend\Framework\Bootstrap\Event as BootstrapEvent;
use Zend\Framework\EventManager\EventInterface as Event;

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
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $bootstrap = new BootstrapEvent;

        $bootstrap->setEventTarget($event->getEventTarget())
                  ->setServiceManager($sm);

        $em->trigger($bootstrap);
    }
}
