<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use RuntimeException;
use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\Manager\GeneratorTrait as EventGenerator;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\AliasTrait as Alias;
use Zend\Framework\Service\Factory\FactoryTrait as Factory;
use Zend\Framework\Service\Manager\ManagerTrait as ServiceManager;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManagerInterface;
use Zend\Framework\View\Exception\EventInterface as Exception;
use Zend\Framework\View\Model\EventInterface as Model;
use Zend\Framework\View\Model\Render\EventInterface as Render;
use Zend\Framework\View\Renderer\EventInterface as Renderer;
use Zend\View\Model\ModelInterface as ViewModel;

class Manager
    implements EventManagerInterface,
               ExceptionInterface,
               ManagerInterface,
               PluginInterface,
               RenderInterface,
               RendererInterface,
               ServiceManagerInterface
{
    /**
     *
     */
    use Alias,
        EventGenerator,
        EventManager,
        Factory,
        ServiceManager;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config    = $config;
        $this->alias     = $config->view()->aliases();
        $this->listeners = $config->listeners();
        $this->services  = $config->services();
    }

    /**
     * @param array|EventInterface|string $event
     * @return EventInterface
     */
    protected function event($event)
    {
        return $event instanceof EventInterface ? $event : $this->create($event);
    }

    /**
     * @param \Exception $exception
     * @param ViewModel $viewModel
     * @return mixed
     * @throws RuntimeException
     */
    public function exception(\Exception $exception, ViewModel $viewModel)
    {
        $viewModel = $this->trigger([Exception::EVENT, $exception], $viewModel);

        if (!$viewModel) {
            throw new RuntimeException('Could not find view model for View\Exception');
        }

        return $this->render($viewModel);
    }

    /**
     * @param array|callable|string $listener
     * @param null $options
     * @return callable
     */
    protected function listener($listener, $options = null)
    {
        return is_callable($listener) ? $listener : $this->create($listener, $options);
    }

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function plugin($name, $options = null)
    {
        return $this->get($this->alias(strtolower($name)), $options);
    }

    /**
     * @param ViewModel $viewModel
     * @param null $options
     * @return mixed
     */
    public function render(ViewModel $viewModel, $options = null)
    {
        return $this->trigger([Render::EVENT, $viewModel], $options);
    }

    /**
     * @param ViewModel $viewModel
     * @param null $options
     * @return mixed
     */
    public function renderer(ViewModel $viewModel, $options = null)
    {
        return $this->trigger([Renderer::EVENT, $viewModel], $options);
    }

    /**
     * @param mixed $source
     * @param null $options
     * @return ViewModel
     */
    public function viewModel($source, $options = null)
    {
        return $this->trigger([Model::EVENT, $source], $options);
    }
}
