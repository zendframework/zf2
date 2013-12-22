<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Render;

use Zend\Framework\View\View;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\Renderer\Renderer;

use Zend\Framework\MvcEvent;

class Event
    extends MvcEvent
{
    /**
     * @var string
     */
    protected $eventName = MvcEvent::EVENT_RENDER;

    /**
     * @var ViewModel
     */
    protected $model;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var View
     */
    protected $view;

    /**
     * @return bool|View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function getViewModel()
    {
        return $this->model;
    }

    /**
     * @param ViewModel $model
     * @return $this
     */
    public function setViewModel(ViewModel $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param Renderer $renderer
     * @return $this
     */
    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
}
