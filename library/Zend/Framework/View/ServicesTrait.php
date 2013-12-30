<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\View\Config as ViewConfig;
use Zend\Framework\View\Manager as ViewManager;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\Listener as View;
use Zend\Framework\View\Plugin\Manager as ViewPluginManager;
use Zend\View\Resolver\ResolverInterface as ViewResolver;

use Zend\Framework\View\Renderer\ServicesTrait as Renderer;

trait ServicesTrait
{
    /**
     *
     */
    use Renderer;

    /**
     * @return ViewManager
     */
    public function viewManager()
    {
        return $this->service('View\Manager');
    }

    /**
     * @param ViewManager $vm
     * @return self
     */
    public function setViewManager(ViewManager $vm)
    {
        return $this->add('View\Manager', $vm);
    }

    /**
     * @return ViewConfig
     */
    public function viewConfig()
    {
        return $this->viewManager()->viewConfig();
    }

    /**
     * @return bool|ViewResolver
     */
    public function viewResolver()
    {
        return $this->service('View\Resolver');
    }

    /**
     * @param ViewResolver $resolver
     * @return self
     */
    public function setViewResolver(ViewResolver $resolver)
    {
        return $this->add('View\Resolver', $resolver);
    }

    /**
     * @return bool|ViewModel
     */
    public function viewModel()
    {
        return $this->service('View\Model');
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel)
    {
        return $this->add('View\Model', $viewModel);
    }

    /**
     * @return bool|ViewPluginManager
     */
    public function viewPluginManager()
    {
        return $this->service('View\Plugin\Manager');
    }

    /**
     * @return bool|View
     */
    public function view()
    {
        return $this->service('View');
    }

    /**
     * @param View $view
     * @return self
     */
    public function setView(View $view)
    {
        return $this->add('View', $view);
    }
}
