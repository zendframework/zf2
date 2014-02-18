<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\View\Model;

use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ModelInterface as ViewModel;

trait ServicesTrait
{
    /**
     * @param $controller
     * @param RouteMatch $routeMatch
     * @return bool|ViewModel
     *
     */
    public function controllerViewModel($controller, RouteMatch $routeMatch)
    {
        return $this->sm->get('Controller\View\Model', [$controller, $routeMatch]);
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setControllerViewModel(ViewModel $viewModel)
    {
        return $this->sm->add('Controller\View\Model', $viewModel);
    }
}
