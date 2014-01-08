<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Dispatch\Error\Event as DispatchError;
use Zend\Framework\Dispatch\Event as Dispatch;
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
    public function __construct($event = self::EVENT_APPLICATION, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        $dispatch = new Dispatch;

        $dispatch->setTarget($event->target())
                 ->setRouteMatch($this->routeMatch())
                 ->setViewModel($this->viewModel);

        try {

            $this->em->__invoke($dispatch);

            return $dispatch->result();

        } catch (DispatchException $exception) {

            $error = new DispatchError;

            $error->setTarget($event->target())
                  ->setException($exception->exception())
                  ->setControllerName($exception->controllerName())
                  ->setControllerClass($exception->controllerClass());

            $this->em->__invoke($error);
        }
    }
}
