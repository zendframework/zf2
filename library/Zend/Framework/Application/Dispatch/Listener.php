<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch;

use Exception;
use Zend\Framework\Application\EventInterface;
use Zend\Framework\Controller\Event as Controller;
use Zend\Framework\Application\Dispatch\Error\Event as DispatchError;

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
        $controllerName = $event->routeMatch()->getParam('controller', 'not-found');

        $controller = $this->controllerManager->controller( $controllerName );

        $this->em->push($controller);

        $dispatch = new Controller;

        $dispatch->setTarget($controller)
                 ->setViewModel($this->viewModel);

        try {

            $this->em->__invoke($dispatch);

        } catch (Exception $exception) {

            $error = new DispatchError;

            $error->setTarget($event->target())
                  ->setException($exception)
                  ->setControllerName($controllerName)
                  ->setControllerClass(get_class($controller));

            $this->em->__invoke($error);

        }

        return $dispatch->result();
    }
}
