<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\View\Http;

use Zend\Framework\EventManager\AbstractListenerAggregate;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\EventManager\ManagerInterface as EventManager;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\Application;
use Zend\Framework\Render\Exception as RenderException;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\ModuleManager\ModuleEventListener;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;
use Zend\View\View;

class DefaultRenderingStrategy
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var array
     */
    protected $name = [
        MvcEvent::EVENT_RENDER,
        MvcEvent::EVENT_RENDER_ERROR
    ];

    /**
     * @var int
     */
    protected $priority = -10000;

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
        $this->setView($sm->getView());

        return $this;
    }

    /**
     * @param $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Set layout template value
     *
     * @param  string $layoutTemplate
     * @return DefaultRenderingStrategy
     */
    /*public function setLayoutTemplate($layoutTemplate)
    {
        $this->layoutTemplate = (string) $layoutTemplate;
        return $this;
    }*/

    /**
     * Get layout template value
     *
     * @return string
     */
    /*public function getLayoutTemplate()
    {
        return $this->layoutTemplate;
    }*/

    /**
     * Render the view
     *
     * @param  MvcEvent $e
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

        $view = $this->view;
        $view->setRequest($request);
        $view->setResponse($response);

        $view->render($viewModel);

        return $response;
    }
}
