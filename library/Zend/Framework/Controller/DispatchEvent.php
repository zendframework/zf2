<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\Framework\Controller\AbstractActionController as ActionController;
use Zend\Framework\View\Model\ViewModel as ViewModel;

class DispatchEvent
    extends MvcEvent
{
    /**
     * @var string
     */
    protected $eventName = MvcEvent::EVENT_CONTROLLER_DISPATCH;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var object
     */
    protected $controller;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * @param $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $controller
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return object
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getControllerClass()
    {
        return get_class($this->controller);
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ViewModel $viewModel
     * @return $this
     */
    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    /**
     * @return ViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * @param Listener $listener
     * @return bool
     */
    public function __invoke(Listener $listener)
    {
        $response = $listener($this);

        if ($listener instanceof ActionController) {
            $this->setResult($response);
        }

        return $this->eventStopPropagation;
    }
}
