<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\View;

use Zend\Framework\View\Event as ViewEvent;
use Zend\Framework\Mvc\EventInterface;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

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
    public function __construct($event = self::EVENT_MVC_APPLICATION, $target = null, $priority = null)
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
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }

        $viewModel = $event->getViewModel();
        if (!$viewModel instanceof ViewModel) {
            return;
        }

        $render = new ViewEvent;

        $render->setTarget($event->target())
               ->setServiceManager($sm)
               ->setViewModel($viewModel);

        try {

            $em->__invoke($render);

        } catch(Exception $exception) {

            $error = new ViewErrorEvent;

            $error->setTarget($event->target())
                ->setException($exception);

            $em->__invoke($error);
        }
    }
}
