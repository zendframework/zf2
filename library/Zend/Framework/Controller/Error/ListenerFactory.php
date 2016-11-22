<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Error;

use Zend\Framework\Route\Manager\ServicesTrait as Route;
use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\View\Manager\ServicesTrait as ViewManager;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use Route,
        ViewManager;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function __invoke(Request $request, array $options = [])
    {
        $viewModel = $this->viewModel()
                          ->setTemplate($this->config()->view()->notFoundTemplate());

        return (new Listener)->setViewModel($viewModel);
    }
}
