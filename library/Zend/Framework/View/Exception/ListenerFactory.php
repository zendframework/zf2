<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Exception;

use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\View\Model\ServicesTrait as ViewModel;
use Zend\Framework\View\ServicesConfigTrait as ViewConfig;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use ViewConfig,
        ViewModel;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function __invoke(Request $request, array $options = [])
    {
        $viewModel = $this->viewModel()
                          ->setTemplate($this->exceptionTemplate())
                          ->setVariable('display_exceptions', $this->displayExceptions());

        return (new Listener)->setViewModel($viewModel);
    }
}
