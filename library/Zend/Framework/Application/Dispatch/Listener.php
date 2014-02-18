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
        $controller = $event->controller();
        $request    = $event->request();
        $response   = $event->response();

        $this->listeners()->push(Controller::EVENT, $controller);

        try {

            $response = $this->trigger([Controller::EVENT, $controller], [$request, $response]);

        } catch (Exception $exception) {

            $response = $this->trigger(['Dispatch\Error', $controller], $exception);

        }

        return $response;
    }
}
