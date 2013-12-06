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
use Zend\Framework\EventManager\EventManagerInterface as EventManager;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\Application;
use Zend\Framework\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;
use Zend\View\View;

class DefaultRenderingStrategy extends AbstractListenerAggregate
{
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
     * Set view
     *
     * @param  View $view
     * @return DefaultRenderingStrategy
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManager $events)
    {
        $this->listeners[] = $events->attach(new CallbackListener(array($this, 'render'), MvcEvent::EVENT_RENDER, null, -10000));
        $this->listeners[] = $events->attach(new CallbackListener(array($this, 'render'), MvcEvent::EVENT_RENDER_ERROR, null, -10000));
    }

    /**
     * Set layout template value
     *
     * @param  string $layoutTemplate
     * @return DefaultRenderingStrategy
     */
    public function setLayoutTemplate($layoutTemplate)
    {
        $this->layoutTemplate = (string) $layoutTemplate;
        return $this;
    }

    /**
     * Get layout template value
     *
     * @return string
     */
    public function getLayoutTemplate()
    {
        return $this->layoutTemplate;
    }

    /**
     * Render the view
     *
     * @param  MvcEvent $e
     * @return Response
     */
    public function render(Event $e)
    {
        $result = $e->getResult();
        if ($result instanceof Response) {
            return $result;
        }

        // Martial arguments
        $request   = $e->getRequest();
        $response  = $e->getResponse();
        $viewModel = $e->getViewModel();
        if (!$viewModel instanceof ViewModel) {
            return;
        }

        $view = $this->view;
        $view->setRequest($request);
        $view->setResponse($response);

        try {
            $view->render($viewModel);
        } catch (\Exception $ex) {
            var_dump($ex);
            if ($e->getName() === MvcEvent::EVENT_RENDER_ERROR) {
                throw $ex;
            }

            $application = $e->getApplication();
            $events      = $application->getEventManager();
            $e->setError(Application::ERROR_EXCEPTION)
              ->setParam('exception', $ex);
            $events->trigger(MvcEvent::EVENT_RENDER_ERROR, $e);
        }

        return $response;
    }
}
