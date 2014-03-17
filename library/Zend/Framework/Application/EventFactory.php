<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface;
use Zend\Framework\View\Manager\ServicesTrait as ViewManager;

class EventFactory
    extends Factory
{
    /**
     *
     */
    use ViewManager;

    /**
     * @param RequestInterface $request
     * @param array $listeners
     * @return Event
     */
    public function __invoke(RequestInterface $request, array $listeners = [])
    {
        $viewModel = $this->rootViewModel()
                          ->setTemplate($this->config()->view()->layoutTemplate())
                          ->setTerminal(true);

        return (new Event($this->sm))->setViewModel($viewModel);
    }
}
