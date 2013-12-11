<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\View\Renderer\PhpRenderer as ViewPhpRenderer;
use Zend\View\Resolver as ViewResolver;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\Mvc\View\Http\RouteNotFoundStrategy;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Mvc\View\Http\DefaultRenderingStrategy;
use Zend\Framework\View\Model\CreateViewModelListener;
use Zend\Framework\View\Template\Listener as TemplateListener;
use Zend\Framework\View\Model\Listener as ViewModelListener;

class Listener extends EventListener
{
    protected $name = MvcEvent::EVENT_DISPATCH;

    protected $priority = 10000;

    public function __invoke(Event $event)
    {
        $em = $event->getEventManager();
        $sm = $event->getServiceManager();

        $viewConfig = $event->getViewConfig();

        $resolver = $event->getViewResolver();

        $pm = $event->getViewPluginManager();

        $view = $event->getView();

        $viewModel = $event->getViewModel();

        $viewModel->setTemplate($viewConfig->getLayoutTemplate());

        $renderer = $this->getRenderer($viewModel, $resolver, $pm);
        $sm->add('View\Renderer', $renderer);

        $em->attach($this->getExceptionStrategy($viewConfig)); //MvcEvent::EVENT_DISPATCH_ERROR 1

        $em->attach(new ViewModelListener); //MvcEvent::EVENT_CONTROLLER_DISPATCH -100
        $em->attach($this->getRouteNotFoundStrategy($viewConfig)); //MvcEvent::EVENT_CONTROLLER_DISPATCH -90
        $em->attach(new TemplateListener); //MvcEvent::EVENT_CONTROLLER_DISPATCH -90
        $em->attach(new CreateViewModelListener); //MvcEvent::EVENT_CONTROLLER_DISPATCH -80

        $em->attach(new DefaultRenderingStrategy($view)); //Mvc::EVENT_RENDER -10000

        $em->attach(new PhpRendererStrategy($renderer)); //ViewEvent::EVENT_RENDERER 1, ViewEvent::EVENT_RESPONSE 1

        foreach($viewConfig->getMvcStrategies() as $listener) {
            $em->attach($sm->get(new ServiceRequest($listener)));
        }

        foreach($viewConfig->getStrategies() as $listener) {
            $em->attach($sm->get(new ServiceRequest($listener)));
        }
    }

    public function getRenderer($viewModel, $resolver, $pm)
    {
        $renderer = new ViewPhpRenderer;
        $renderer->setHelperPluginManager($pm);
        $renderer->setResolver($resolver);

        $modelHelper = $renderer->plugin('viewmodel');
        $modelHelper->setRoot($viewModel);

        return $renderer;
    }

    public function getExceptionStrategy($config)
    {
        $exceptionStrategy = new ExceptionStrategy();

        $exceptionStrategy->setDisplayExceptions($config->get('display_exceptions'));

        $exceptionStrategy->setExceptionTemplate($config->get('exception_template'));

        return $exceptionStrategy;
    }

    public function getRouteNotFoundStrategy($config)
    {
        $routeNotFoundStrategy = new RouteNotFoundStrategy();

        $routeNotFoundStrategy->setDisplayExceptions($config->get('display_exceptions'));
        $routeNotFoundStrategy->setDisplayNotFoundReason($config->get('display_not_found_reason'));
        $routeNotFoundStrategy->setNotFoundTemplate($config->get('not_found_template'));

        return $routeNotFoundStrategy;
    }
}
