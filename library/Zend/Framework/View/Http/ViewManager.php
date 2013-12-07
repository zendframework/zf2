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
use Zend\Mvc\View\Http\CreateViewModelListener;
use Zend\Mvc\View\Http\InjectTemplateListener;
use Zend\Mvc\View\Http\InjectViewModelListener;
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
        $this->listeners[] = $em->attach(new CallbackListener(array($this, 'onBootstrap'), MvcEvent::EVENT_BOOTSTRAP, null, 10000));
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

        $services     = $application->getServiceManager();

        $config       = $application->getConfig()['view_manager'];

        $em = $application->getEventManager();

        $routeNotFoundStrategy = $this->getRouteNotFoundStrategy($config, $services);

        $exceptionStrategy = $this->getExceptionStrategy($config, $services);

        $layoutTemplate = $this->getLayoutTemplate($config);

        $resolver = $services->get(new ServiceRequest('ViewResolver'));
        $viewHelperManager = $services->get(new ServiceRequest('ViewHelperManager'));
        $viewModel = $event->getViewModel();

        $viewModel->setTemplate($layoutTemplate);

        $renderer = $this->getRenderer($viewModel, $resolver, $viewHelperManager);
        $services->add('View\Renderer', $renderer);

        $rendererStrategy = new PhpRendererStrategy($renderer);

        $view = $this->getView($em, $rendererStrategy, $services);

        $mvcRenderingStrategy    = $this->getMvcRenderingStrategy($view, $layoutTemplate, $services);

        $createViewModelListener = new CreateViewModelListener();
        $injectTemplateListener  = new InjectTemplateListener();
        $injectViewModelListener = new InjectViewModelListener();

        $this->registerMvcRenderingStrategies($config, $em, $services);
        $this->registerViewStrategies($config, $view, $services);

        $em->attach($routeNotFoundStrategy);
        $em->attach($exceptionStrategy);

        $em->attach(new CallbackListener(array($injectViewModelListener, 'injectViewModel'), MvcEvent::EVENT_DISPATCH_ERROR, null, -100));
        $em->attach(new CallbackListener(array($injectViewModelListener, 'injectViewModel'), MvcEvent::EVENT_RENDER_ERROR, null, -100));

        $em->attach($mvcRenderingStrategy);

        $em->attach(new CallbackListener(array($createViewModelListener, 'createViewModelFromArray'), MvcEvent::EVENT_CONTROLLER_DISPATCH, 'Zend\Stdlib\DispatchableInterface', -80));
        $em->attach(new CallbackListener(array($routeNotFoundStrategy, 'prepareNotFoundViewModel'), MvcEvent::EVENT_CONTROLLER_DISPATCH, 'Zend\Stdlib\DispatchableInterface', -90));
        $em->attach(new CallbackListener(array($createViewModelListener, 'createViewModelFromNull'), MvcEvent::EVENT_CONTROLLER_DISPATCH, 'Zend\Stdlib\DispatchableInterface', -80));
        $em->attach(new CallbackListener(array($injectTemplateListener, 'injectTemplate'), MvcEvent::EVENT_CONTROLLER_DISPATCH, 'Zend\Stdlib\DispatchableInterface', -90));
        $em->attach(new CallbackListener(array($injectViewModelListener, 'injectViewModel'), MvcEvent::EVENT_CONTROLLER_DISPATCH, 'Zend\Stdlib\DispatchableInterface', -100));
    }

    public function getView($em, $rendererStrategy, $services)
    {
        $view = new View();

        $view->setEventManager($em);

        $em->attach($rendererStrategy);

        $services->add('View', $view);

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

    public function getLayoutTemplate($config)
    {
        $layout = 'layout/layout';

        if (isset($config['layout'])) {
            $layout = $config['layout'];
        }

        return $layout;
    }

    public function getMvcRenderingStrategy($view, $layoutTemplate, $services)
    {
        $mvcRenderingStrategy = new DefaultRenderingStrategy($view);
        $mvcRenderingStrategy->setLayoutTemplate($layoutTemplate);

        //$services->setService('View\DefaultRenderingStrategy', $mvcRenderingStrategy);

        return $mvcRenderingStrategy;
    }

    public function getExceptionStrategy($config, $services)
    {
        $exceptionStrategy = new ExceptionStrategy();

        if (isset($config['display_exceptions'])) {
            $exceptionStrategy->setDisplayExceptions($config['display_exceptions']);
        }

        if (isset($config['exception_template'])) {
            $exceptionStrategy->setExceptionTemplate($config['exception_template']);
        }

        //$services->add('View\ExceptionStrategy', $exceptionStrategy);

        return $exceptionStrategy;
    }

    public function getRouteNotFoundStrategy($config, $services)
    {
        $routeNotFoundStrategy = new RouteNotFoundStrategy();

        if (isset($config['display_exceptions'])) {
            $routeNotFoundStrategy->setDisplayExceptions($config['display_exceptions']);
        }

        if (isset($config['display_not_found_reason'])) {
            $routeNotFoundStrategy->setDisplayNotFoundReason($config['display_not_found_reason']);
        }

        if (isset($config['not_found_template'])) {
            $routeNotFoundStrategy->setNotFoundTemplate($config['not_found_template']);
        }

        //$services->add('RouteNotFoundStrategy', $routeNotFoundStrategy);

        return $routeNotFoundStrategy;
    }

    protected function registerMvcRenderingStrategies($config, EventManager $events, $services)
    {
        if (!isset($config['mvc_strategies'])) {
            return;
        }

        $mvcStrategies = $config['mvc_strategies'];

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

    protected function registerViewStrategies($config, $view, $services)
    {
        if (!isset($config['strategies'])) {
            return;
        }

        $strategies = $config['strategies'];

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
