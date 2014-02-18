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
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventManager;

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

            $response = $this->trigger([Controller::EVENT, $controller, $routeMatch], [$request, $response]);

        } catch (Exception $exception) {

            $response = $this->trigger([Error::EVENT, $controller, $routeMatch], $exception);

        }

        return $response;
    }
}
