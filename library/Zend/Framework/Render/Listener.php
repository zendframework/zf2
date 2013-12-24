<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Render;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_RENDER, $target = null, $priority = null)
    {
        $this->eventName = $event;
    }

    /**
     * Render the view
     *
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }

        // Martial arguments
        $request   = $event->getRequest();
        $response  = $event->getResponse();
        $viewModel = $event->getViewModel();
        if (!$viewModel instanceof ViewModel) {
            return;
        }

        $view = $event->getView();
        $view->setRequest($request);
        $view->setResponse($response);

        $view->render($viewModel);
    }
}
