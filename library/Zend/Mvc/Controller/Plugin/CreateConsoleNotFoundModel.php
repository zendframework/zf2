<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\View\Model\ConsoleModel;
use Zend\Stdlib\ResponseInterface;

/**
 * Create a console view model representing a "not found" action
 */
class CreateConsoleNotFoundModel extends AbstractPlugin
{
    /**
     * @param  ResponseInterface $response
     * @return ConsoleModel
     */
    public function __invoke($response)
    {
        $viewModel = new ConsoleModel();
        $viewModel->setErrorLevel(1);
        $viewModel->setResult('Page not found');

        return $viewModel;
    }
}
