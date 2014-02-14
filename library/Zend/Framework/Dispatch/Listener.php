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
use Zend\Framework\Controller\EventInterface as ControllerEvent;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\Service\ServiceTrait As ServiceManager;
use Zend\Mvc\Router\RouteMatch;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventManager,
        EventListener,
        ServiceManager,
        ServicesTrait;

    /**
     * @param EventInterface $event
     * @param RouteMatch $routeMatch
     * @return mixed
     */
    public function __invoke(EventInterface $event, RouteMatch $routeMatch)
    {
        $controller = $this->controller($routeMatch);

        $this->listeners()->push(ControllerEvent::EVENT, $controller);

        try {

            $response = $this->trigger(['Controller\Event', $controller]);

        } catch (Exception $exception) {

            $response = $this->trigger(['Dispatch\Error', $controller, $routeMatch, $exception]);

        }

        return $response;
    }
}
