<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Error;

use Zend\Framework\Controller\View\Model\ServicesTrait as ControllerViewModel;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\View\Model\ServicesTrait as ViewModel;
use Zend\Framework\View\ServicesConfigTrait as ViewConfig;
use Zend\Framework\View\ServicesTrait as ViewManager;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use ControllerViewModel,
        Route,
        ViewConfig,
        ViewManager,
        ViewModel;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function __invoke(Request $request, array $options = [])
    {
        $viewModel = $this->controllerViewModel($this->routeMatch())
                          ->setTemplate($this->notFoundTemplate());

        return (new Listener)->setControllerViewModel($viewModel)
                             ->setViewModel($this->viewModel());
    }
}
