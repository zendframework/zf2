<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Exception;
use Zend\Framework\Controller\Event as Controller;
use Zend\Framework\Dispatch\Exception as DispatchException;

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
    public function __construct($event = self::EVENT_DISPATCH, $target = null, $priority = null)
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
        $cm = $this->controllerManager;
        $rm = $event->routeMatch();
        $vm = $event->viewModel();

        $controllerName = $rm->getParam('controller', 'not-found');

        $controller = $cm->controller( $controllerName );

        $this->em->push($controller);

        $dispatch = new Controller;

        $dispatch->setTarget($controller)
                 ->setViewModel($vm);

        try {

            $this->em->__invoke($dispatch);

            $event->setResult($dispatch->result());

        } catch (Exception $exception) {

            $dispatchException = new DispatchException;

            $dispatchException->setControllerName($controllerName)
                              ->setControllerClass(get_class($controller))
                              ->setException($exception);

            throw $dispatchException;
        }
    }
}
