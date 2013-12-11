<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Http;

use Traversable;
use Zend\Framework\EventManager\AbstractListenerAggregate;
use Zend\Framework\EventManager\EventManagerInterface as EventManager;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\View\HelperPluginManager as ViewHelperManager;
use Zend\View\Renderer\PhpRenderer as ViewPhpRenderer;
use Zend\View\Resolver as ViewResolver;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\View;
use Zend\Mvc\View\Http\RouteNotFoundStrategy;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Mvc\View\Http\DefaultRenderingStrategy;
use Zend\Framework\View\Model\CreateViewModelListener;
use Zend\Framework\View\Template\Listener as TemplateListener;
use Zend\Framework\View\Model\Listener as ViewModelListener;
use Zend\Framework\View\ViewManagerInterface;

use Zend\Framework\ServiceManager\ConfigInterface as Config;

/**
 * Prepares the view layer
 *
 * Instantiates and configures all classes related to the view layer, including
 * the renderer (and its associated resolver(s) and helper manager), the view
 * object (and its associated rendering strategies), and the various MVC
 * strategies and listeners.
 *
 * Defines and manages the following services:
 *
 * - ViewHelperManager (also aliased to Zend\View\HelperPluginManager)
 * - ViewTemplateMapResolver (also aliased to Zend\View\Resolver\TemplateMapResolver)
 * - ViewTemplatePathStack (also aliased to Zend\View\Resolver\TemplatePathStack)
 * - ViewResolver (also aliased to Zend\View\Resolver\AggregateResolver and ResolverInterface)
 * - ViewRenderer (also aliased to Zend\View\Renderer\PhpRenderer and RendererInterface)
 * - ViewPhpRendererStrategy (also aliased to Zend\View\Strategy\PhpRendererStrategy)
 * - View (also aliased to Zend\View\View)
 * - DefaultRenderingStrategy (also aliased to Zend\Mvc\View\Http\DefaultRenderingStrategy)
 * - ExceptionStrategy (also aliased to Zend\Mvc\View\Http\ExceptionStrategy)
 * - RouteNotFoundStrategy (also aliased to Zend\Mvc\View\Http\RouteNotFoundStrategy and 404Strategy)
 * - ViewModel
 */
class ViewManager extends AbstractListenerAggregate implements ViewManagerInterface
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getViewHelpersConfig()
    {
        return $this->config->get('view_helpers');
    }

    public function attach(EventManager $em)
    {
        $this->listeners[] = $em->attach(new CallbackListener(array($this, 'onBootstrap'), MvcEvent::EVENT_DISPATCH, null, 10000));
    }

    public function detach(EventManager $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onBootstrap($event)
    {
        $application = $event->getApplication();

        $services = $application->getServiceManager();

        $config = $this->config;

        $em = $application->getEventManager();

        $layoutTemplate = $this->config->get('layout_template');

        $resolver = $services->get(new ServiceRequest('ViewResolver'));

        $viewHelperManager = $services->get(new ServiceRequest('ViewHelperManager'));

        $viewModel = $event->getViewModel();
        $viewModel->setTemplate($layoutTemplate);

        $renderer = $this->getRenderer($viewModel, $resolver, $viewHelperManager);
        $services->add('View\Renderer', $renderer);

        $view = $this->getView($em, $services);

        $em->attach(new PhpRendererStrategy($renderer));
        $em->attach($this->getRouteNotFoundStrategy($config, $services));
        $em->attach($this->getExceptionStrategy($config, $services));
        $em->attach(new ViewModelListener); //-100
        $em->attach(new TemplateListener); //-90
        $em->attach(new CreateViewModelListener); //-80
        $em->attach(new DefaultRenderingStrategy($view)); //Mvc::EVENT_RENDER -10000

        if ($config->get('mvc_strategies')) {
            $this->registerMvcRenderingStrategies($config->get('mvc_strategies'), $em, $services);
        }

        if ($config->get('strategies')) {
            $this->registerViewStrategies($config->get('strategies'), $view, $services);
        }
    }

    public function getView($em, $services)
    {
        $view = new View;

        $view->setEventManager($em);

        //$services->add('View', $view);

        return $view;
    }

    public function getRenderer($viewModel, $resolver, $pluginManager)
    {
        $renderer = new ViewPhpRenderer;
        $renderer->setHelperPluginManager($pluginManager);
        $renderer->setResolver($resolver);

        $modelHelper = $renderer->plugin('viewmodel');
        $modelHelper->setRoot($viewModel);

        return $renderer;
    }

    public function getExceptionStrategy($config, $services)
    {
        $exceptionStrategy = new ExceptionStrategy();

        $exceptionStrategy->setDisplayExceptions($config->get('display_exceptions'));

        $exceptionStrategy->setExceptionTemplate($config->get('exception_template'));

        //$services->add('View\ExceptionStrategy', $exceptionStrategy);

        return $exceptionStrategy;
    }

    public function getRouteNotFoundStrategy($config, $services)
    {
        $routeNotFoundStrategy = new RouteNotFoundStrategy();

        $routeNotFoundStrategy->setDisplayExceptions($config->get('display_exceptions'));
        $routeNotFoundStrategy->setDisplayNotFoundReason($config->get('display_not_found_reason'));
        $routeNotFoundStrategy->setNotFoundTemplate($config->get('not_found_template'));

        //$services->add('RouteNotFoundStrategy', $routeNotFoundStrategy);

        return $routeNotFoundStrategy;
    }

    protected function registerMvcRenderingStrategies($mvcStrategies, EventManager $events, $services)
    {
        if (is_string($mvcStrategies)) {
            $mvcStrategies = array($mvcStrategies);
        }

        if (!is_array($mvcStrategies) && !$mvcStrategies instanceof Traversable) {
            return;
        }

        foreach ($mvcStrategies as $mvcStrategy) {
            if (!is_string($mvcStrategy)) {
                continue;
            }

            $listener = $services->get(new ServiceRequest($mvcStrategy));
            if ($listener instanceof ListenerAggregateInterface) {
                $events->attach($listener);
            }
        }
    }

    protected function registerViewStrategies($strategies, $view, $services)
    {
        if (is_string($strategies)) {
            $strategies = array($strategies);
        }

        if (!is_array($strategies) && !$strategies instanceof Traversable) {
            return;
        }

        foreach ($strategies as $strategy) {
            if (!is_string($strategy)) {
                continue;
            }

            $listener = $services->get(new ServiceRequest($strategy));
            if ($listener instanceof ListenerAggregateInterface) {
                $view->getEventManager()->attach($listener);
            }
        }
    }
}
