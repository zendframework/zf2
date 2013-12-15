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
use Zend\Framework\MvcEvent;

use Zend\Framework\ServiceManager\CreateServiceTrait as CreateService;

class MvcListener
    extends EventListener
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_NAME;
    /**
     *
     */
    use CreateService;

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $bootstrap = new BootstrapEvent;

        $bootstrap->setTarget($event->getTarget())
                  ->setServiceManager($sm);

        $em->trigger($bootstrap);
    }
}
