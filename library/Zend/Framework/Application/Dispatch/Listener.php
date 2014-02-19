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
use Zend\Framework\Controller\Error\EventInterface as Error;
use Zend\Framework\Controller\EventInterface as Controller;
use Zend\Framework\Controller\Manager\ServiceTrait as ControllerManager;
use Zend\Framework\Controller\NotFound\EventInterface as NotFound;

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

            if (!$this->dispatchable($controller)) {
                return $this->dispatch([NotFound::EVENT, $routeMatch], $controller);
            }

            return $this->dispatch([Controller::EVENT, $controller, $routeMatch], [$request, $response]);

        } catch (Exception $exception) {

            return $this->dispatch([Error::EVENT, $controller, $routeMatch, $exception]);

        }
    }
}
