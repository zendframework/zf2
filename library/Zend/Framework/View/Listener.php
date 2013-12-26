<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\View\Renderer\Event as ViewRendererEvent;
use Zend\Framework\View\Response\Event as ViewResponseEvent;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\EventManager\EventInterface;

class Listener
    implements ListenerInterface, EventListenerInterface, FactoryInterface
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
    public function __construct($event = self::EVENT_VIEW, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     * @throws DispatchException
     */
    public function __invoke(EventInterface $event)
    {
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $renderer = new ViewRendererEvent;

        $renderer->setTarget($event->target())
                 ->setServiceManager($sm);

        $em->__invoke($renderer);

        $response = new ViewResponseEvent;

        $response->setTarget($event->target())
                 ->setServiceManager($sm)
                 //->setResult($renderer->getResult())
                 ->setResult($renderer->getResult())
                 ->setViewRenderer($renderer->getViewRenderer());

        $em->__invoke($response);
    }
}
