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
use Zend\Framework\MvcEvent;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

use Zend\View\Renderer\PhpRenderer as ViewPhpRenderer;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\Mvc\View\Http\RouteNotFoundStrategy;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\View\View;
use Zend\Framework\View\Model\CreateViewModelListener as CreateViewModelListener;
use Zend\Framework\View\Model\Listener as ViewModelListener;
use Zend\Framework\View\Template\Listener as TemplateListener;
use Zend\Mvc\View\Http\DefaultRenderingStrategy;

use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_BOOTSTRAP;

    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $sm = $event->getServiceManager();

        $config = $event->getViewConfig();

        $em = $event->getEventManager();

        $layoutTemplate = $config->get('layout_template');

        $resolver = $sm->get(new ServiceRequest('ViewResolver'));

        $viewHelperManager = $sm->get(new ServiceRequest('ViewPluginManager'));

        $viewModel = $event->getViewModel();
        $viewModel->setTemplate($layoutTemplate);

        $renderer = $this->getRenderer($viewModel, $resolver, $viewHelperManager);
        $sm->add('View\Renderer', $renderer);

        $view = $this->getView($em, $sm);

        $em->attach(new PhpRendererStrategy($renderer));
        $em->attach($this->getRouteNotFoundStrategy($config, $sm));
        $em->attach($this->getExceptionStrategy($config, $sm));
        $em->attach(new ViewModelListener); //-100
        $em->attach(new TemplateListener); //-90
        $em->attach(new CreateViewModelListener); //-80
        $em->attach(new DefaultRenderingStrategy($view)); //Mvc::EVENT_RENDER -10000

        if ($config->get('mvc_strategies')) {
            $this->registerMvcRenderingStrategies($config->get('mvc_strategies'), $em, $sm);
        }

        if ($config->get('strategies')) {
            $this->registerViewStrategies($config->get('strategies'), $em, $sm);
        }
    }

    public function getView($em, $sm)
    {
        $view = new View;

        $view->setEventManager($em);

        //$sm->add('View', $view);

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

    public function getExceptionStrategy($config, $sm)
    {
        $exceptionStrategy = new ExceptionStrategy();

        $exceptionStrategy->setDisplayExceptions($config->get('display_exceptions'));

        $exceptionStrategy->setExceptionTemplate($config->get('exception_template'));

        //$sm->add('View\ExceptionStrategy', $exceptionStrategy);

        return $exceptionStrategy;
    }

    public function getRouteNotFoundStrategy($config, $sm)
    {
        $routeNotFoundStrategy = new RouteNotFoundStrategy();

        $routeNotFoundStrategy->setDisplayExceptions($config->get('display_exceptions'));
        $routeNotFoundStrategy->setDisplayNotFoundReason($config->get('display_not_found_reason'));
        $routeNotFoundStrategy->setNotFoundTemplate($config->get('not_found_template'));

        //$sm->add('RouteNotFoundStrategy', $routeNotFoundStrategy);

        return $routeNotFoundStrategy;
    }

    protected function registerMvcRenderingStrategies($mvcStrategies, EventManager $em, $sm)
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

            $listener = $sm->get(new ServiceRequest($mvcStrategy));
            if ($listener instanceof ListenerAggregateInterface) {
                $em->attach($listener);
            }
        }
    }

    protected function registerViewStrategies($strategies, $em, $sm)
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

            $listener = $sm->get(new ServiceRequest($strategy));
            if ($listener instanceof ListenerAggregateInterface) {
                $em->attach($listener);
            }
        }
    }
}
