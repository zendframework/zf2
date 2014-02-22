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
use Zend\Framework\Application\Dispatch\Error\EventInterface as DispatchError;
use Zend\Framework\Controller\Error\EventInterface as ControllerError;
use Zend\Framework\Controller\EventInterface as Controller;
use Zend\Framework\Controller\Manager\ServiceTrait as ControllerManager;


class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ControllerManager;

    /**
     * @param EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        $request    = $event->request();
        $response   = $event->response();
        $routeMatch = $event->routeMatch();
        $controller = $routeMatch->getParam('controller');

        try {

            $event = $this->dispatchable($controller) ? Controller::EVENT : ControllerError::EVENT;

            return $this->dispatch([$event, $routeMatch, $controller], [$request, $response]);

        } catch (Exception $exception) {
;
            return $this->dispatch([DispatchError::EVENT, $exception], [$request, $response]);

        }
    }
}
