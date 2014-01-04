<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\View\Response\Event as ViewResponseEvent;

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
    public function __construct($event = self::EVENT_VIEW, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     */
    public function __invoke(EventInterface $event)
    {
        $this->em    = $event->eventManager();
        $this->sm    = $event->serviceManager();
        $this->event = $event;

        $rendered = $this->render($event->viewModel());

        $responseEvent = new ViewResponseEvent;
        $responseEvent->setServiceManager($this->sm)
                      ->setTarget($this->event->target())
                      ->setResult($rendered);

        $this->em->__invoke($responseEvent);
    }
}
