<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\View\ServicesConfigTrait as ViewConfig;
use Zend\Framework\View\Model\ServicesTrait as ViewModel;

class EventFactory
    extends Factory
{
    /**
     *
     */
    use ViewConfig,
        ViewModel;

    /**
     * @param Request $request
     * @param array $listeners
     * @return Event
     */
    public function __invoke(Request $request, array $listeners = [])
    {
        $viewModel = $this->rootViewModel()
                          ->setTemplate($this->layoutTemplate())
                          ->setTerminal(true);

        return (new Event($this->sm))->setViewModel($viewModel);
    }
}
