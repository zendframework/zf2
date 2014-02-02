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
use Zend\Framework\Controller\Manager\ServiceTrait as ControllerManager;
use Zend\Framework\Dispatch\Error\Event as DispatchError;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\Mvc\Router\RouteMatch;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ControllerManager,
        EventManager,
        EventListener,
        ViewModel;

    /**
     * @param EventInterface $event
     * @param RouteMatch $routeMatch
     * @return mixed
     */
    public function trigger(EventInterface $event, RouteMatch $routeMatch)
    {
        $controllerName = $routeMatch->getParam('controller', 'not-found');

        $controller = $this->controllerManager->controller($controllerName, [$routeMatch]);

        $controllerEvent = new Controller($controller);

        $this->em->push($controllerEvent->name(), $controller);

        try {

            $response = $this->em->trigger($controllerEvent);

        } catch (Exception $exception) {

            $error = new DispatchError;

            $error->setTarget($event->target())
                  ->setException($exception)
                  ->setControllerName($controllerName)
                  ->setControllerClass(get_class($controller));

            $this->em->trigger($error);

        }

        return $response;
    }
}
