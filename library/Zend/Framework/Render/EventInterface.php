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
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\Renderer\Renderer;
use Zend\Framework\View\View;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT_RENDER = 'mvc.render';
    /**
     *
     */
    const EVENT_RENDER_ERROR = 'mvc.render.error';

    /**
     * @return Renderer
     */
    public function getRenderer();

    /**
     * @param Renderer $renderer
     * @return $this
     */
    public function setRenderer(Renderer $renderer);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param mixed $result
     * @return $this
     */
    public function setResult($result);

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceManager(ServiceManager $sm);

    /**
     * @return View
     */
    public function getView();

    /**
     * @param View $view
     * @return $this
     */
    public function setView(View $view);

    /**
     * @return ViewModel
     */
    public function getViewModel();
    /**
     * @param ViewModel $viewModel
     * @return $this
     */
    public function setViewModel(ViewModel $viewModel);
}
