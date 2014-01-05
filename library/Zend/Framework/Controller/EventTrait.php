<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\Controller\ListenerInterface as Controller;
use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\Response\ServicesTrait as Response;
use Zend\Framework\View\Model\ViewModel as ViewModel;
use Zend\Framework\View\ServicesTrait as View;

trait EventTrait
{
    /**
     *
     */
    use Event, Response, Services, View;

    /**
     * @var string
     */
    protected $error;

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
    public function error()
    {
        return $this->error;
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
    public function result()
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
    public function viewModel()
    {
        return $this->viewModel;
    }
}
