<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Dispatch;

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
        $controller = $event->controller();
        $request    = $event->request();
        $response   = $event->response();
        $routeMatch = $event->routeMatch();

        try {

            if (!$controller || !$this->dispatchable($controller)) {
                return $this->error($routeMatch, $controller, $request, $response);
            }

            return $this->dispatch($controller, $routeMatch, $request, $response);

        } catch (\Exception $exception) {

            return $this->exception($exception, $request, $response);

        }
    }
}
