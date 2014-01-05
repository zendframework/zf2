<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\EventManager\Manager\ListenerInterface as EventManager;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Controller\Manager\Listener as ControllerManager;
use Zend\Mvc\Router\RouteMatch as RouteMatch;
use Zend\View\Model\ModelInterface as ViewModel;

trait EventTrait
{
    /**
     *
     */
    use Event;

    /**
     * @var ControllerManager
     */
    protected $cm;

    /**
     * @var EventManager
     */
    protected $em;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var ViewModel
     */
    protected $vm;

    /**
     * @return bool|ControllerManager
     */
    public function controllerManager()
    {
        return $this->cm;
    }

    /**
     * @param ControllerManager $cm
     * @return self
     */
    public function setControllerManager(ControllerManager $cm)
    {
        $this->cm = $cm;
        return $this;
    }

    /**
     * @return EventManager
     */
    public function eventManager()
    {
        return $this->em;
    }

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em)
    {
        $this->em = $em;
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
     * @param $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return bool|RouteMatch
     */
    public function routeMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * @return bool|ViewModel
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
}
