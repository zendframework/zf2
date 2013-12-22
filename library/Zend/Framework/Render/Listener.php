<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Render;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\Render\Exception as RenderException;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\View;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

use Zend\Framework\EventManager\Listener as ParentListener;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends ParentListener
    implements FactoryInterface
{
    /**
     * @var array
     */
    //protected $eventName = [
        //MvcEvent::EVENT_RENDER,
        //MvcEvent::EVENT_RENDER_ERROR
    //];

    /**
     * @var int
     */
    //protected $eventPriority = -10000;

    /**
     * Layout template - template used in root ViewModel of MVC event.
     *
     * @var string
     */
    protected $layoutTemplate = 'layout';

    /**
     * @var View
     */
    protected $view;

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }

    /**
     * Render the view
     *
     * @param Event $event
     * @return Response
     */
    public function __invoke(Event $event)
    {
        $result = $event->getResult();
        if ($result instanceof Response) {
            return $result;
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

        return $response;
    }
}
