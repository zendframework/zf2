<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Dispatch;

use Zend\Framework\ApplicationServiceTrait as Services;
use Zend\Framework\Controller\Dispatch\ListenerInterface as Controller;
use Zend\Framework\EventManager\EventTrait as EventService;
use Zend\Framework\View\Model\ViewModel as ViewModel;

trait EventTrait
{
    /**
     *
     */
    use EventService, Services;

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
     * @return self
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
     * @return self
     */
    public function setController(Controller $controller)
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
     * @return self
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
     * @return self
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
}
