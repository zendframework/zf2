<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\View\ServicesTrait as View;
use Zend\View\Model\ModelInterface as ViewModel;
use Zend\View\Renderer\RendererInterface as ViewRenderer;

trait EventTrait
{
    /**
     *
     */
    use Event, View;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var ViewRenderer
     */
    protected $renderer;

    /**
     * @var ViewModel
     */
    protected $vm;

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return ViewModel
     */
    public function viewModel()
    {
        return $this->vm;
    }

    /**
     * @param ViewModel $vm
     * @return self
     */
    public function setViewModel(ViewModel $vm)
    {
        $this->vm = $vm;
        return $this;
    }

    /**
     * @return bool|ViewRenderer
     */
    public function viewRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param ViewRenderer $renderer
     * @return self
     */
    public function setViewRenderer(ViewRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
}
