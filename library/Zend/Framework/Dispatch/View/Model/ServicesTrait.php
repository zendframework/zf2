<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch\View\Model;

use Zend\Framework\Controller\ListenerInterface as Controller;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ModelInterface as ViewModel;

trait ServicesTrait
{
    /**
     * @param Controller $controller
     * @param RouteMatch $routeMatch
     * @return bool|ViewModel
     *
     */
    public function dispatchViewModel(Controller $controller, RouteMatch $routeMatch)
    {
        return $this->sm->get('Dispatch\View\Model', [$controller, $routeMatch]);
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setDispatchViewModel(ViewModel $viewModel)
    {
        return $this->sm->add('Dispatch\View\Model', $viewModel);
    }
}
