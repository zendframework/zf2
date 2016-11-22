<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Exception;
use Zend\View\Model\ModelInterface as ViewModel;

trait ServiceTrait
{
    /**
     * @var ManagerInterface|PluginInterface|RenderInterface|RendererInterface
     */
    protected $vm;

    /**
     * @param Exception $exception
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function exception(Exception $exception, ViewModel $viewModel)
    {
        return $this->vm->exception($exception, $viewModel);
    }

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function plugin($name, $options = null)
    {
        return $this->vm->plugin($name, $options);
    }

    /**
     * @param ViewModel $viewModel
     * @param null $options
     * @return mixed
     */
    public function render(ViewModel $viewModel, $options = null)
    {
        return $this->vm->render($viewModel, $options);
    }

    /**
     * @param ViewModel $viewModel
     * @param null $options
     * @return mixed
     */
    public function renderer(ViewModel $viewModel, $options = null)
    {
        return $this->vm->renderer($viewModel, $options);
    }

    /**
     * @param ManagerInterface $vm
     * @return self
     */
    public function setViewManager(ManagerInterface $vm)
    {
        $this->vm = $vm;
        return $this;
    }

    /**
     * @return ManagerInterface
     */
    public function viewManager()
    {
        return $this->vm;
    }
}